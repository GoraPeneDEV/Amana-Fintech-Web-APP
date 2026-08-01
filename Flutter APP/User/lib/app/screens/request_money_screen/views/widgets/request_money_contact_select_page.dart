import 'package:flutter/gestures.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/animated_widget/expanded_widget.dart';
import 'package:ovopay/app/components/buttons/custom_elevated_button.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/custom_contact_list_tile_card.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/app/components/image/my_network_image_widget.dart';
import 'package:ovopay/app/components/image/random_color_avatar.dart';
import 'package:ovopay/app/components/no_data.dart';
import 'package:ovopay/app/components/text-field/rounded_text_field.dart';
import 'package:ovopay/app/components/text/header_text.dart';
import 'package:ovopay/app/components/text/small_text.dart';
import 'package:ovopay/app/screens/request_money_screen/controller/request_money_controller.dart';
import 'package:ovopay/core/data/controller/contact/contact_controller.dart';
import 'package:skeletonizer/skeletonizer.dart';

import '../../../../../core/utils/util_exporter.dart';

class RequestMoneyContactSelectPage extends StatelessWidget {
  const RequestMoneyContactSelectPage({
    super.key,
    required this.onSuccessCallback,
  });
  final VoidCallback onSuccessCallback;

  @override
  Widget build(BuildContext context) {
    return GetBuilder<RequestMoneyController>(
      builder: (requestMoneyController) {
        return GetBuilder<ContactController>(
          builder: (contactController) {
            return NestedScrollView(
              headerSliverBuilder: (
                BuildContext context,
                bool innerBoxIsScrolled,
              ) {
                return <Widget>[
                  SliverToBoxAdapter(
                    child: Padding(
                      padding: EdgeInsets.only(bottom: Dimensions.space16.h),
                      child: CustomAppCard(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            HeaderText(
                              textAlign: TextAlign.center,
                              text: MyStrings.to.tr,
                              textStyle: MyTextStyle.sectionTitle.copyWith(
                                color: MyColor.getHeaderTextColor(),
                              ),
                            ),
                            spaceDown(Dimensions.space8),
                            RoundedTextField(
                              controller: requestMoneyController.phoneNumberOrUserNameController,
                              showLabelText: false,
                              labelText: MyStrings.usernameOrPhoneHint.tr,
                              hintText: MyStrings.usernameOrPhoneHint.tr,
                              textInputAction: TextInputAction.next,
                              keyboardType: TextInputType.text,
                              suffixIcon: IconButton(
                                onPressed: () {
                                  requestMoneyController.checkUserExist(
                                    onSuccessCallback,
                                  );
                                },
                                icon: MyAssetImageWidget(
                                  color: MyColor.getPrimaryColor(),
                                  width: 20.sp,
                                  height: 20.sp,
                                  boxFit: BoxFit.contain,
                                  assetPath: MyIcons.arrowForward,
                                  isSvg: true,
                                ),
                              ),
                              onChanged: (value) {
                                contactController.filterContacts(value);
                              },
                            ),
                            if (requestMoneyController.phoneNumberOrUserNameController.text.isEmptyString && requestMoneyController.latestRequestMoneyHistory.isNotEmpty) ...[
                              spaceDown(Dimensions.space16),
                              HeaderText(
                                text: MyStrings.recent.tr,
                                textStyle: MyTextStyle.sectionTitle2.copyWith(
                                  color: MyColor.getHeaderTextColor(),
                                ),
                              ),
                              spaceDown(Dimensions.space8),
                              ...List.generate(
                                requestMoneyController.latestRequestMoneyHistory.length,
                                (index) {
                                  var item = requestMoneyController.latestRequestMoneyHistory[index];
                                  bool isLastIndex = index == requestMoneyController.latestRequestMoneyHistory.length - 1;
                                  return CustomContactListTileCard(
                                    leading: MyNetworkImageWidget(
                                      width: Dimensions.space40.w,
                                      height: Dimensions.space40.w,
                                      radius: Dimensions.radiusProMax.r,
                                      isProfile: true,
                                      imageUrl: item.requestReceiver?.getUserImageUrl() ?? "",
                                      imageAlt: item.requestReceiver?.getFullName() ?? "",
                                    ),
                                    showBorder: !isLastIndex,
                                    imagePath: item.requestReceiver?.getUserImageUrl(),
                                    title: item.requestReceiver?.getFullName(),
                                    subtitle: "+${item.requestReceiver?.getUserMobileNo(withCountryCode: true)}",
                                    onPressed: () {
                                      requestMoneyController.checkUserExist(
                                        onSuccessCallback,
                                        inputUserNameOrPhone: item.requestReceiver?.username ?? "",
                                      );
                                    },
                                  );
                                },
                              ),
                            ],
                          ],
                        ),
                      ),
                    ),
                  ),
                ];
              },
              body: requestMoneyController.getPhoneOrUsername.isEmptyString == false && contactController.filterContact.isEmpty
                  ? SingleChildScrollView(
                      child: CustomAppCard(
                        child: buildSendMoneyTapToContinue(
                          requestMoneyController,
                        ),
                      ),
                    )
                  : CustomAppCard(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.center,
                        children: [
                          Align(
                            alignment: AlignmentDirectional.centerStart,
                            child: HeaderText(
                              text: MyStrings.allContact.tr,
                              textStyle: MyTextStyle.sectionTitle2.copyWith(
                                color: MyColor.getHeaderTextColor(),
                              ),
                            ),
                          ),
                          spaceDown(Dimensions.space16),
                          if (contactController.isLoading == true || contactController.filterContact.isNotEmpty) ...[
                            Expanded(
                              child: CustomScrollView(
                                slivers: [
                                  contactController.isLoading
                                      ? SliverToBoxAdapter(
                                          child: Skeletonizer(
                                            enabled: true,
                                            child: ListView.builder(
                                              shrinkWrap: true,
                                              itemCount: 7,
                                              itemBuilder: (context, index) {
                                                return ListTile(
                                                  leading: SizedBox(
                                                    width: Dimensions.space40.w,
                                                    height: Dimensions.space40.h,
                                                    child: Icon(
                                                      Icons.ac_unit,
                                                      size: 40,
                                                    ),
                                                  ),
                                                  title: Text("Demo User"),
                                                  subtitle: Text(
                                                    "+16565656556465",
                                                  ),
                                                );
                                              },
                                            ),
                                          ),
                                        )
                                      : SliverList(
                                          delegate: SliverChildBuilderDelegate(
                                            (BuildContext context, int index) {
                                              final contact = contactController.filterContact[index];
                                              bool isLastIndex = index == contactController.filterContact.length - 1; // Use this if childCount is dynamic
                                              return contact.phones.length == 1
                                                  ? CustomContactListTileCard(
                                                      leading: RandomColorAvatar(
                                                        color: avatarColors[index % avatarColors.length],
                                                        width: Dimensions.space40.w,
                                                        height: Dimensions.space40.h,
                                                        name: contact.displayName.isEmptyString ? contact.phones.first.number : contact.displayName,
                                                      ),
                                                      title: contact.displayName.isEmptyString ? contact.phones.first.number : contact.displayName,
                                                      showBorder: isLastIndex == false,
                                                      subtitle: contact.phones.first.number,
                                                      onPressed: () {
                                                        requestMoneyController.phoneNumberOrUserNameController.text = (contact.phones.first.number);
                                                        // requestMoneyController.checkUserExist(onSuccessCallback);
                                                      },
                                                    )
                                                  : Column(
                                                      children: [
                                                        CustomContactListTileCard(
                                                          leading: RandomColorAvatar(
                                                            color: avatarColors[index % avatarColors.length],
                                                            width: Dimensions.space40.w,
                                                            height: Dimensions.space40.h,
                                                            name: contact.displayName.isEmptyString ? contact.phones.first.number : contact.displayName,
                                                          ),
                                                          title: contact.displayName.isEmptyString ? contact.phones.first.number : contact.displayName,
                                                          showBorder: isLastIndex == false,
                                                          subtitle: "${contact.phones.length} ${MyStrings.savedNumbers.tr}",
                                                          onPressed: () {
                                                            contactController.toggleSelectedExpandIndex(
                                                              index,
                                                            );
                                                          },
                                                        ),
                                                        ExpandedSection(
                                                          expand: contactController.selectedExpandIndex == index,
                                                          child: Column(
                                                            children: List.generate(
                                                              contact.phones.length,
                                                              (index) {
                                                                final phoneNumber = contact.phones[index];
                                                                return Material(
                                                                  color: MyColor.getScreenBgColor(),
                                                                  borderRadius: BorderRadius.circular(
                                                                    12,
                                                                  ),
                                                                  type: MaterialType.card,
                                                                  child: ListTile(
                                                                    shape: Border(
                                                                      bottom: BorderSide(
                                                                        color: MyColor.getBorderColor(),
                                                                        width: 1,
                                                                      ),
                                                                    ),
                                                                    leading: SizedBox(
                                                                      width: Dimensions.space10.w,
                                                                    ),
                                                                    title: SmallText(
                                                                      text: phoneNumber.number,
                                                                      textStyle: MyTextStyle.sectionSubTitle1.copyWith(
                                                                        color: MyColor.getBodyTextColor(),
                                                                      ),
                                                                    ),
                                                                    onTap: () {
                                                                      requestMoneyController.phoneNumberOrUserNameController.text = (phoneNumber.number);
                                                                      // requestMoneyController.checkUserExist(onSuccessCallback);
                                                                    },
                                                                  ),
                                                                );
                                                              },
                                                            ),
                                                          ),
                                                        ),
                                                      ],
                                                    );
                                            },
                                            childCount: contactController.filterContact.length, // Number of items
                                          ),
                                        ),
                                ],
                              ),
                            ),
                          ] else ...[
                            Expanded(
                              child: FittedBox(
                                fit: BoxFit.scaleDown,
                                child: NoDataWidget(
                                  text: MyStrings.noContactFound,
                                ),
                              ),
                            ),
                          ],
                        ],
                      ),
                    ),
            );
          },
        );
      },
    );
  }

  Widget buildSendMoneyTapToContinue(
    RequestMoneyController sendMoneyController,
  ) {
    return Column(
      children: [
        spaceDown(Dimensions.space20),
        RichText(
          text: TextSpan(
            text: "${MyStrings.requestMoney.tr} ${MyStrings.to.tr} ",
            style: MyTextStyle.sectionSubTitle1,
            children: <TextSpan>[
              TextSpan(
                text: sendMoneyController.getPhoneOrUsername,
                style: MyTextStyle.sectionSubTitle1.copyWith(
                  fontWeight: FontWeight.w600,
                  color: MyColor.getHeaderTextColor(),
                ),
                recognizer: TapGestureRecognizer()..onTap = () {},
              ),
            ],
          ),
        ),
        spaceDown(Dimensions.space20),
        CustomElevatedBtn(
          radius: Dimensions.largeRadius.r,
          isLoading: sendMoneyController.isCheckingUserLoading,
          bgColor: MyColor.getPrimaryColor(),
          text: MyStrings.tapToContinue.tr,
          onTap: () {
            sendMoneyController.checkUserExist(onSuccessCallback);
          },
        ),
        spaceDown(Dimensions.space20),
      ],
    );
  }
}
