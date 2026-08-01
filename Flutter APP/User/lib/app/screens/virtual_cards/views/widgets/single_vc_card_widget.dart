import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/animation/shake_animation.dart';
import 'package:ovopay/app/components/bottom-sheet/bottom_sheet_header_row.dart';
import 'package:ovopay/app/components/bottom-sheet/custom_bottom_sheet_plus.dart';
import 'package:ovopay/app/components/buttons/custom_elevated_button.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/column_widget/card_column.dart' show CardColumn;
import 'package:ovopay/app/components/dialog/app_dialog.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/app/components/text-field/rounded_text_field.dart';
import 'package:ovopay/core/data/models/modules/virtual_cards/virtual_cards_response_model.dart';
import 'package:ovopay/core/data/services/shared_pref_service.dart';
import 'package:ovopay/core/utils/util_exporter.dart';
import 'package:ovopay/app/components/credit_card_ui/u_credit_card.dart';

import '../../controller/virtual_cards_controller.dart';

class SingleVcCardWidget extends StatefulWidget {
  const SingleVcCardWidget({super.key});

  @override
  State<SingleVcCardWidget> createState() => _SingleVcCardWidgetState();
}

class _SingleVcCardWidgetState extends State<SingleVcCardWidget> {
  @override
  Widget build(BuildContext context) {
    return GetBuilder<VirtualCardsController>(
      builder: (controller) {
        var item = controller.singleCardInfoData ?? CardDataModel();
        return CustomAppCard(
          child: Column(
            children: [
              CreditCardUi(
                enableFlipping: true,
                currencySymbol: SharedPreferenceService.getCurrencySymbol(),
                cardHolderFullName: item.cardHolder?.name ?? "",
                cardNumber: item.cardNumber ?? "************${item.last4 ?? ""}",
                shouldMaskCardNumber: false,
                showCopyButton: item.cardNumber != null,
                cardProviderLogo: MyAssetImageWidget(
                  height: item.status == AppStatus.VIRTUAL_CARD_CANCELED ? Dimensions.space63 : Dimensions.space35,
                  width: null,
                  assetPath: item.status == AppStatus.VIRTUAL_CARD_CANCELED ? MyImages.canceledImage : MyImages.appLogo,
                  color: item.status == AppStatus.VIRTUAL_CARD_CANCELED ? null : MyColor.white,
                  boxFit: BoxFit.contain,
                ),
                validFrom: '${DateConverter.estimatedDateOrTime(item.createdAt ?? DateTime.now().toIso8601String(), customFormat: "MM")}/${DateConverter.estimatedDateOrTime(item.createdAt ?? DateTime.now().toIso8601String(), customFormat: "yy")}',
                validThru: item.formatCardExpiry(),
                showValidThru: true,
                balance: AppConverter.formatNumberDouble(item.balance ?? ""),
                autoHideBalance: true,
                cvvNumber: item.cvcNumber ?? "***",
                showBalance: true,
                topLeftColor: MyColor.getPrimaryColor(),
                doesSupportNfc: false,
                backgroundDecorationImage: DecorationImage(
                  fit: BoxFit.cover,
                  image: AssetImage(MyImages.vcCardBgImage),
                ),
                placeNfcIconAtTheEnd: true,
                creditCardType: CreditCardType.visa,
                cardType: CardType.debit,
              ).shakeX(
                condition: item.cardNumber != null,
                duration: Duration(milliseconds: 1000),
              ),
              spaceDown(Dimensions.space20),
              Row(
                children: [
                  Expanded(
                    child: buildVCardActionIcon(
                      iconWidget: Icon(
                        item.cardNumber == null ? Icons.visibility_outlined : Icons.visibility_off_outlined,
                        size: Dimensions.space24.w,
                        color: MyColor.getHeaderTextColor(),
                      ),
                      titleText: MyStrings.view.tr,
                      onTap: () async {
                        if (item.cardNumber != null) {
                          controller.clearConfidentialData();
                          return;
                        }
                        await AppDialogs.pinVerificationPopUpWidget(
                          context,
                          onSuccess: (value) async {},
                          onSubmitClick: (String pin) async {
                            await controller.viewConfidentialVirtualCardInfo(
                              cardID: item.id?.toString() ?? "-1",
                              pin: pin,
                            );
                          },
                        );
                        return;
                      },
                    ),
                  ),
                  Expanded(
                    child: buildVCardActionIcon(
                      iconWidget: Icon(
                        Icons.add,
                        size: Dimensions.space24.w,
                        color: item.status != AppStatus.VIRTUAL_CARD_ACTIVE ? MyColor.getBorderColor() : MyColor.getHeaderTextColor(),
                      ),
                      titleText: MyStrings.addFund.tr,
                      titleTextColor: item.status != AppStatus.VIRTUAL_CARD_ACTIVE ? MyColor.getBorderColor() : null,
                      onTap: () {
                        if (item.status != AppStatus.VIRTUAL_CARD_ACTIVE) {
                          return;
                        }
                        CustomBottomSheetPlus(
                          child: SafeArea(
                            child: buildAddAmountSectionSheet(
                              item: item,
                              context: context,
                            ),
                          ),
                        ).show(context);
                      },
                    ),
                  ),
                  Expanded(
                    child: buildVCardActionIcon(
                      iconWidget: MyAssetImageWidget(
                        isSvg: true,
                        assetPath: MyIcons.profileInactive,
                        color: MyColor.getHeaderTextColor(),
                        width: Dimensions.space24.w,
                        height: Dimensions.space24.w,
                      ),
                      titleText: MyStrings.cardHolder.tr,
                      onTap: () {
                        CustomBottomSheetPlus(
                          child: SafeArea(
                            child: buildHolderDetailsSectionSheet(
                              item: item,
                              context: context,
                            ),
                          ),
                        ).show(context);
                      },
                    ),
                  ),
                  Expanded(
                    child: buildVCardActionIcon(
                      iconWidget: Icon(
                        Icons.close,
                        size: Dimensions.space24.w,
                        color: item.status == AppStatus.VIRTUAL_CARD_CANCELED ? MyColor.getBorderColor() : MyColor.redLightColor,
                      ),
                      titleText: item.status == AppStatus.VIRTUAL_CARD_CANCELED ? MyStrings.canceled.tr : MyStrings.cancel.tr,
                      titleTextColor: item.status == AppStatus.VIRTUAL_CARD_CANCELED ? MyColor.getBorderColor() : null,
                      onTap: () async {
                        if (item.status == AppStatus.VIRTUAL_CARD_CANCELED) {
                          return;
                        }
                        AppDialogs.confirmDialogForAll(
                          subTitle: MyStrings.areYouSureWantToCancelThisCard,
                          context,
                          isConfirmLoading: controller.isCancelVirtualCard,
                          onConfirmTap: () {
                            controller.cancelVirtualCardBalance(
                              cardID: (item.id ?? -1).toString(),
                            );
                          },
                        );
                      },
                    ),
                  ),
                ],
              ),
              spaceDown(Dimensions.space5),
            ],
          ),
        );
      },
    );
  }

  Widget buildVCardActionIcon({
    required Widget iconWidget,
    String titleText = "",
    Color? titleTextColor,
    VoidCallback? onTap,
  }) {
    return InkWell(
      onTap: onTap,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.center,
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          CustomAppCard(
            backgroundColor: MyColor.getWhiteColor(),
            padding: EdgeInsets.all(Dimensions.space10),
            width: Dimensions.space50.h,
            height: Dimensions.space50.h,
            radius: Dimensions.space12,
            child: iconWidget,
          ),
          spaceDown(Dimensions.space4),
          Text(
            titleText,
            textAlign: TextAlign.center,
            style: MyTextStyle.caption2Style.copyWith(
              color: titleTextColor ?? MyColor.getHeaderTextColor(),
              fontWeight: FontWeight.w600,
            ),
          ),
        ],
      ),
    );
  }

  Widget buildAddAmountSectionSheet({
    required CardDataModel item,
    required BuildContext context,
  }) {
    return GetBuilder<VirtualCardsController>(
      builder: (controller) {
        return PopScope(
          onPopInvokedWithResult: (didPop, result) {
            controller.clearCreationData();
          },
          child: SingleChildScrollView(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                BottomSheetHeaderRow(header: MyStrings.addFund),
                spaceDown(Dimensions.space24),
                RoundedTextField(
                  controller: controller.amountController,
                  labelText: MyStrings.enterAmount.tr,
                  hintText: '${AppConverter.formatNumberDouble(controller.globalChargeModel?.minLimit ?? "0")}-${AppConverter.formatNumberDouble(controller.globalChargeModel?.maxLimit ?? "0")}',
                  textInputAction: TextInputAction.done,
                  keyboardType: TextInputType.numberWithOptions(decimal: true),
                  textStyle: MyTextStyle.headerH3.copyWith(
                    color: MyColor.getHeaderTextColor(),
                  ),
                  focusBorderColor: MyColor.getPrimaryColor(),
                  textInputFormatter: [
                    FilteringTextInputFormatter.allow(
                      RegExp(r'[0-9.]'),
                    ), // Allows digits and a decimal point
                    MaxValueInputFormatter(
                      maxValue: AppConverter.formatNumberDouble(
                        controller.globalChargeModel?.maxLimit ?? "0",
                      ),
                    ), // Max Input digit
                  ],
                  onChanged: (value) {
                    controller.onChangeAmountControllerText(value);
                  },
                  validator: (value) {
                    return MyUtils().validateAmountForm(
                      value: value ?? '0',
                      userCurrentBalance: 0,
                      showCurrentBalance: false,
                      minLimit: AppConverter.formatNumberDouble(
                        controller.globalChargeModel?.minLimit ?? "0",
                      ),
                      maxLimit: AppConverter.formatNumberDouble(
                        controller.globalChargeModel?.maxLimit ?? "0",
                      ),
                    );
                  },
                ),
                spaceDown(Dimensions.space8),
                Text.rich(
                  TextSpan(
                    children: [
                      TextSpan(
                        text: "${MyStrings.available.tr}: ",
                        style: MyTextStyle.sectionBodyTextStyle.copyWith(
                          color: MyColor.getBodyTextColor(),
                        ),
                      ),
                      TextSpan(
                        text: "${controller.userCurrentBalance}",
                        style: MyTextStyle.sectionBodyBoldTextStyle.copyWith(
                          color: MyColor.getPrimaryColor(),
                        ),
                      ),
                    ],
                  ),
                ),
                spaceDown(Dimensions.space15),
                AnimatedSwitcher(
                  duration: const Duration(
                    milliseconds: 300,
                  ), // Duration for animation
                  transitionBuilder: (child, animation) {
                    return FadeTransition(opacity: animation, child: child);
                  },
                  child: controller.mainAmount > 0
                      ? Column(
                          key: ValueKey(
                            'visibleWidget',
                          ), // Key to differentiate between states
                          children: [
                            buildChargeRow(
                              firstText: MyStrings.amount.tr,
                              lastText: "${AppConverter.formatNumber(controller.getAmount.toString(), precision: 2)} ${SharedPreferenceService.getCurrencySymbol(isFullText: true)}",
                            ),
                            buildChargeRow(
                              firstText: MyStrings.charge.tr,
                              lastText: "${AppConverter.formatNumber(controller.totalCharge.toString(), precision: 2)} ${SharedPreferenceService.getCurrencySymbol(isFullText: true)}",
                            ),
                            buildChargeRow(
                              firstText: MyStrings.total.tr,
                              lastText: "${AppConverter.formatNumber(controller.payableAmountText.toString(), precision: 2)} ${SharedPreferenceService.getCurrencySymbol(isFullText: true)}",
                            ),
                            spaceDown(Dimensions.space15),
                          ],
                        )
                      : SizedBox(
                          key: ValueKey('hiddenWidget'),
                        ), // Empty widget when hidden
                ),
                spaceDown(Dimensions.space15),
                CustomElevatedBtn(
                  isLoading: controller.isAddBalanceLoading,
                  radius: Dimensions.largeRadius.r,
                  bgColor: MyColor.getPrimaryColor(),
                  text: MyStrings.confirm,
                  onTap: () async {
                    await controller.addVirtualCardBalance(
                      cardID: (item.id ?? -1).toString(),
                    );
                  },
                ),
              ],
            ),
          ),
        );
      },
    );
  }

  Widget buildHolderDetailsSectionSheet({
    required CardDataModel item,
    required BuildContext context,
  }) {
    var cardHolder = item.cardHolder;
    return PopScope(
      onPopInvokedWithResult: (didPop, result) {},
      child: SingleChildScrollView(
        child: Column(
          children: [
            BottomSheetHeaderRow(header: MyStrings.details),
            spaceDown(Dimensions.space10),
            Row(
              children: [
                Expanded(
                  child: CardColumn(
                    headerTextStyle: MyTextStyle.caption1Style.copyWith(
                      color: MyColor.getBodyTextColor(),
                    ),
                    bodyTextStyle: MyTextStyle.sectionTitle3.copyWith(
                      fontSize: Dimensions.fontLarge,
                      color: MyColor.getHeaderTextColor(),
                    ),
                    header: MyStrings.cardName.tr,
                    body: cardHolder?.name ?? '',
                    space: 5,
                    crossAxisAlignment: CrossAxisAlignment.start,
                  ),
                ),
                Container(
                  height: Dimensions.space50,
                  width: 1,
                  color: MyColor.getBorderColor(),
                  margin: const EdgeInsets.symmetric(horizontal: 10),
                ),
                Expanded(
                  child: CardColumn(
                    headerTextStyle: MyTextStyle.caption1Style.copyWith(
                      color: MyColor.getBodyTextColor(),
                    ),
                    bodyTextStyle: MyTextStyle.sectionTitle3.copyWith(
                      fontSize: Dimensions.fontLarge,
                      color: MyColor.getHeaderTextColor(),
                    ),
                    header: MyStrings.dateOfBirth,
                    body: "${cardHolder?.dob?.day ?? ''} / ${cardHolder?.dob?.month ?? ''} / ${cardHolder?.dob?.year ?? ''}",
                    space: 5,
                    crossAxisAlignment: CrossAxisAlignment.end,
                  ),
                ),
              ],
            ),
            spaceDown(Dimensions.space10),
            Container(
              height: 1,
              width: double.infinity,
              color: MyColor.getBorderColor(),
              margin: const EdgeInsets.symmetric(horizontal: 10),
            ),
            spaceDown(Dimensions.space10),
            Row(
              children: [
                Expanded(
                  child: CardColumn(
                    headerTextStyle: MyTextStyle.caption1Style.copyWith(
                      color: MyColor.getBodyTextColor(),
                    ),
                    bodyTextStyle: MyTextStyle.sectionTitle3.copyWith(
                      fontSize: Dimensions.fontLarge,
                      color: MyColor.getHeaderTextColor(),
                    ),
                    header: MyStrings.firstName.tr,
                    body: cardHolder?.firstName ?? '',
                    space: 5,
                    crossAxisAlignment: CrossAxisAlignment.start,
                  ),
                ),
                Container(
                  height: Dimensions.space50,
                  width: 1,
                  color: MyColor.getBorderColor(),
                  margin: const EdgeInsets.symmetric(horizontal: 10),
                ),
                Expanded(
                  child: CardColumn(
                    headerTextStyle: MyTextStyle.caption1Style.copyWith(
                      color: MyColor.getBodyTextColor(),
                    ),
                    bodyTextStyle: MyTextStyle.sectionTitle3.copyWith(
                      fontSize: Dimensions.fontLarge,
                      color: MyColor.getHeaderTextColor(),
                    ),
                    header: MyStrings.lastName,
                    body: cardHolder?.lastName ?? '',
                    space: 5,
                    crossAxisAlignment: CrossAxisAlignment.end,
                  ),
                ),
              ],
            ),
            spaceDown(Dimensions.space10),
            Container(
              height: 1,
              width: double.infinity,
              color: MyColor.getBorderColor(),
              margin: const EdgeInsets.symmetric(horizontal: 10),
            ),
            spaceDown(Dimensions.space10),
            Row(
              children: [
                Expanded(
                  child: CardColumn(
                    headerTextStyle: MyTextStyle.caption1Style.copyWith(
                      color: MyColor.getBodyTextColor(),
                    ),
                    bodyTextStyle: MyTextStyle.sectionTitle3.copyWith(
                      fontSize: Dimensions.fontLarge,
                      color: MyColor.getHeaderTextColor(),
                    ),
                    header: MyStrings.email.tr,
                    body: cardHolder?.email ?? '',
                    isBodyEllipsis: false,
                    space: 5,
                    crossAxisAlignment: CrossAxisAlignment.start,
                  ),
                ),
                Container(
                  height: Dimensions.space50,
                  width: 1,
                  color: MyColor.getBorderColor(),
                  margin: const EdgeInsets.symmetric(horizontal: 10),
                ),
                Expanded(
                  child: CardColumn(
                    headerTextStyle: MyTextStyle.caption1Style.copyWith(
                      color: MyColor.getBodyTextColor(),
                    ),
                    bodyTextStyle: MyTextStyle.sectionTitle3.copyWith(
                      fontSize: Dimensions.fontLarge,
                      color: MyColor.getHeaderTextColor(),
                    ),
                    header: MyStrings.phone,
                    body: "+${cardHolder?.phoneNumber ?? ''}",
                    space: 5,
                    crossAxisAlignment: CrossAxisAlignment.end,
                  ),
                ),
              ],
            ),
            spaceDown(Dimensions.space10),
            Container(
              height: 1,
              width: double.infinity,
              color: MyColor.getBorderColor(),
              margin: const EdgeInsets.symmetric(horizontal: 10),
            ),
            spaceDown(Dimensions.space10),
            Row(
              children: [
                Expanded(
                  child: CardColumn(
                    headerTextStyle: MyTextStyle.caption1Style.copyWith(
                      color: MyColor.getBodyTextColor(),
                    ),
                    bodyTextStyle: MyTextStyle.sectionTitle3.copyWith(
                      fontSize: Dimensions.fontLarge,
                      color: MyColor.getHeaderTextColor(),
                    ),
                    header: MyStrings.state.tr,
                    body: cardHolder?.state ?? '',
                    isBodyEllipsis: false,
                    space: 5,
                    crossAxisAlignment: CrossAxisAlignment.start,
                  ),
                ),
                Container(
                  height: Dimensions.space50,
                  width: 1,
                  color: MyColor.getBorderColor(),
                  margin: const EdgeInsets.symmetric(horizontal: 10),
                ),
                Expanded(
                  child: CardColumn(
                    headerTextStyle: MyTextStyle.caption1Style.copyWith(
                      color: MyColor.getBodyTextColor(),
                    ),
                    bodyTextStyle: MyTextStyle.sectionTitle3.copyWith(
                      fontSize: Dimensions.fontLarge,
                      color: MyColor.getHeaderTextColor(),
                    ),
                    header: MyStrings.zipCode,
                    body: cardHolder?.postalCode ?? '',
                    space: 5,
                    crossAxisAlignment: CrossAxisAlignment.end,
                  ),
                ),
              ],
            ),
            spaceDown(Dimensions.space10),
            Container(
              height: 1,
              width: double.infinity,
              color: MyColor.getBorderColor(),
              margin: const EdgeInsets.symmetric(horizontal: 10),
            ),
            spaceDown(Dimensions.space10),
            Row(
              children: [
                Expanded(
                  child: CardColumn(
                    headerTextStyle: MyTextStyle.caption1Style.copyWith(
                      color: MyColor.getBodyTextColor(),
                    ),
                    bodyTextStyle: MyTextStyle.sectionTitle3.copyWith(
                      fontSize: Dimensions.fontLarge,
                      color: MyColor.getHeaderTextColor(),
                    ),
                    header: MyStrings.city.tr,
                    body: cardHolder?.city ?? '',
                    isBodyEllipsis: false,
                    space: 5,
                    crossAxisAlignment: CrossAxisAlignment.start,
                  ),
                ),
                Container(
                  height: Dimensions.space50,
                  width: 1,
                  color: MyColor.getBorderColor(),
                  margin: const EdgeInsets.symmetric(horizontal: 10),
                ),
                Expanded(
                  child: CardColumn(
                    headerTextStyle: MyTextStyle.caption1Style.copyWith(
                      color: MyColor.getBodyTextColor(),
                    ),
                    bodyTextStyle: MyTextStyle.sectionTitle3.copyWith(
                      fontSize: Dimensions.fontLarge,
                      color: MyColor.getHeaderTextColor(),
                    ),
                    header: MyStrings.country,
                    body: cardHolder?.country ?? '',
                    space: 5,
                    crossAxisAlignment: CrossAxisAlignment.end,
                  ),
                ),
              ],
            ),
            spaceDown(Dimensions.space10),
            Container(
              height: 1,
              width: double.infinity,
              color: MyColor.getBorderColor(),
              margin: const EdgeInsets.symmetric(horizontal: 10),
            ),
            spaceDown(Dimensions.space10),
            Row(
              children: [
                Expanded(
                  child: CardColumn(
                    headerTextStyle: MyTextStyle.caption1Style.copyWith(
                      color: MyColor.getBodyTextColor(),
                    ),
                    bodyTextStyle: MyTextStyle.sectionSubTitle1.copyWith(
                      fontSize: Dimensions.fontLarge,
                      color: MyColor.getHeaderTextColor(),
                    ),
                    header: MyStrings.address.tr,
                    isBodyEllipsis: false,
                    bodyMaxLine: 100,
                    body: cardHolder?.address ?? "---",
                    space: 5,
                    crossAxisAlignment: CrossAxisAlignment.start,
                  ),
                ),
              ],
            ),
            spaceDown(Dimensions.space10),
            Container(
              height: 1,
              width: double.infinity,
              color: MyColor.getBorderColor(),
              margin: const EdgeInsets.symmetric(horizontal: 10),
            ),
          ],
        ),
      ),
    );
  }

  Widget buildChargeRow({required String firstText, required String lastText}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: Dimensions.space5),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Flexible(
            child: Text(
              firstText.tr,
              style: MyTextStyle.sectionTitle3.copyWith(
                color: MyColor.getHeaderTextColor(),
              ),
              overflow: TextOverflow.ellipsis,
              maxLines: 1,
            ),
          ),
          Flexible(
            child: Text(
              lastText.tr,
              maxLines: 2,
              style: MyTextStyle.sectionTitle3.copyWith(
                color: MyColor.getBodyTextColor(),
                fontWeight: FontWeight.normal,
              ),
              overflow: TextOverflow.ellipsis,
              textAlign: TextAlign.end,
            ),
          ),
        ],
      ),
    );
  }
}
