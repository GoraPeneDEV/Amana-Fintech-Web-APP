import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/bottom-sheet/bottom_sheet_header_row.dart';
import 'package:ovopay/app/components/bottom-sheet/custom_bottom_sheet_plus.dart';
import 'package:ovopay/app/components/buttons/custom_elevated_button.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/custom_company_list_tile_card.dart';
import 'package:ovopay/app/components/card/custom_list_tile_card.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/app/components/image/my_network_image_widget.dart';
import 'package:ovopay/app/components/no_data.dart';
import 'package:ovopay/app/components/shimmer/list_tile_shimmer.dart';
import 'package:ovopay/app/components/text-field/rounded_text_field.dart';
import 'package:ovopay/app/components/text/header_text.dart';
import 'package:ovopay/app/screens/bank_transfer_screen/controller/bank_transfer_controller.dart';
import 'package:ovopay/core/data/models/modules/bank_transfer/bank_transfer_info_response_model.dart';
import 'package:ovopay/core/route/route.dart';
import 'package:skeletonizer/skeletonizer.dart';

import '../../../../../core/utils/util_exporter.dart';

class BankTransferSelectBankFromListPageWidget extends StatelessWidget {
  final BuildContext context;
  const BankTransferSelectBankFromListPageWidget({
    super.key,
    required this.context,
    required this.onSuccessCallback,
    required this.onSavedSuccessCallback,
  });
  final VoidCallback onSuccessCallback;
  final VoidCallback onSavedSuccessCallback;
  @override
  Widget build(BuildContext context) {
    return GetBuilder<BankTransferController>(
      builder: (controller) {
        return NestedScrollView(
          headerSliverBuilder: (BuildContext context, bool innerBoxIsScrolled) {
            return <Widget>[
              SliverToBoxAdapter(
                child: Skeletonizer(
                  enabled: controller.isPageLoading,
                  child: Padding(
                    padding: EdgeInsets.only(bottom: Dimensions.space16.h),
                    child: Column(
                      children: [
                        CustomAppCard(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              HeaderText(
                                textAlign: TextAlign.center,
                                text: MyStrings.searchOrganization.tr,
                                textStyle: MyTextStyle.sectionTitle.copyWith(
                                  color: MyColor.getHeaderTextColor(),
                                ),
                              ),
                              spaceDown(Dimensions.space8),
                              RoundedTextField(
                                showLabelText: false,
                                controller: controller.bankNameController,
                                labelText: MyStrings.enterBankNameOrType.tr,
                                hintText: MyStrings.enterBankNameOrType.tr,
                                textInputAction: TextInputAction.search,
                                keyboardType: TextInputType.text,
                                suffixIcon: IconButton(
                                  onPressed: null,
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
                                  controller.filterBankListName(value);
                                },
                              ),
                              if (controller.bankNameController.text.isEmptyString && controller.mySavedBankList.isNotEmpty) ...[
                                spaceDown(Dimensions.space16),
                                HeaderText(
                                  text: MyStrings.savedBankCard.tr,
                                  textStyle: MyTextStyle.sectionTitle2.copyWith(
                                    color: MyColor.getHeaderTextColor(),
                                  ),
                                ),
                                ListView.builder(
                                  shrinkWrap: true,
                                  physics: NeverScrollableScrollPhysics(),
                                  itemCount: controller.getUniqueBankIdList().length,
                                  itemBuilder: (context, index) {
                                    var item = controller.getUniqueBankIdList()[index];
                                    bool isLastIndex = index == controller.getUniqueBankIdList().length - 1;
                                    return CustomListTileCard(
                                      padding: EdgeInsets.symmetric(
                                        vertical: Dimensions.space20.h,
                                      ),
                                      leading: CustomAppCard(
                                        width: Dimensions.space45.w,
                                        height: Dimensions.space45.w,
                                        radius: Dimensions.largeRadius.r,
                                        padding: EdgeInsetsDirectional.all(
                                          Dimensions.space4.w,
                                        ),
                                        child: MyNetworkImageWidget(
                                          boxFit: BoxFit.scaleDown,
                                          imageUrl: item.bank?.getBankImageUrl() ?? "",
                                          isProfile: false,
                                          radius: Dimensions.largeRadius.r,
                                          width: Dimensions.space40.w,
                                          height: Dimensions.space40.w,
                                        ),
                                      ),
                                      subtitleStyle: MyTextStyle.sectionSubTitle1.copyWith(
                                        color: MyColor.getBodyTextColor(),
                                      ),
                                      imagePath: "",
                                      title: item.bank?.name ?? "",
                                      subtitle: "${controller.mySavedBankList.where((value) => value.bank?.id == item.bank?.id).toList().length} ${MyStrings.addedAccount.tr}",
                                      showBorder: !isLastIndex,
                                      onPressed: () {
                                        if (item.bank != null) {
                                          controller.selectBankOnTap(
                                            item.bank!,
                                          );
                                          controller.selectedBankDynamicFormAutofillDataOnTap(
                                            [],
                                          );
                                          CustomBottomSheetPlus(
                                            child: SafeArea(
                                              child: buildBankAddBottomSheet(
                                                bank: item.bank!,
                                              ),
                                            ),
                                          ).show(context);
                                        }
                                      },
                                    );
                                  },
                                ),
                              ],
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ),
            ];
          },
          body: Builder(
            builder: (context) {
              var dataList = controller.filterBankListDataList;
              if (controller.isPageLoading) {
                return ListTileShimmer();
              }
              if (dataList.isEmpty) {
                return SingleChildScrollView(
                  child: CustomAppCard(
                    child: NoDataWidget(text: MyStrings.noMatchingBankFound.tr),
                  ),
                );
              }

              return CustomAppCard(
                width: double.infinity,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    HeaderText(
                      text: MyStrings.bankList.tr,
                      textStyle: MyTextStyle.sectionTitle2.copyWith(
                        color: MyColor.getHeaderTextColor(),
                      ),
                    ),
                    spaceDown(Dimensions.space16),
                    Expanded(
                      child: CustomScrollView(
                        slivers: [
                          SliverList(
                            delegate: SliverChildBuilderDelegate((
                              BuildContext context,
                              int index,
                            ) {
                              var item = dataList[index];
                              bool isLastIndex = index == dataList.length - 1;
                              return CustomListTileCard(
                                padding: EdgeInsets.symmetric(
                                  vertical: Dimensions.space20.h,
                                ),
                                leading: CustomAppCard(
                                  width: Dimensions.space45.w,
                                  height: Dimensions.space45.w,
                                  radius: Dimensions.largeRadius.r,
                                  padding: EdgeInsetsDirectional.all(
                                    Dimensions.space4.w,
                                  ),
                                  child: MyNetworkImageWidget(
                                    radius: Dimensions.largeRadius.r,
                                    boxFit: BoxFit.scaleDown,
                                    imageUrl: item.getBankImageUrl() ?? "",
                                    isProfile: false,
                                    width: Dimensions.space40.w,
                                    height: Dimensions.space40.w,
                                  ),
                                ),
                                imagePath: "",
                                title: "${item.name}",
                                // subtitle: "${item.form?.toJson()}",
                                showBorder: !isLastIndex,
                                onPressed: () {
                                  controller.selectBankOnTap(item);
                                  controller.selectedBankDynamicFormAutofillDataOnTap(
                                    [],
                                  );
                                  CustomBottomSheetPlus(
                                    child: SafeArea(
                                      child: buildBankAddBottomSheet(
                                        bank: item,
                                      ),
                                    ),
                                  ).show(context);
                                },
                              );
                            }, childCount: dataList.length),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              );
            },
          ),
        );
      },
    );
  }

  Widget buildBankAddBottomSheet({required BankDataModel bank}) {
    return PopScope(
      canPop: true,
      child: GetBuilder<BankTransferController>(
        builder: (controller) {
          var dataList = controller.mySavedBankList.where((value) => value.bank?.id == bank.id).toList();
          return ConstrainedBox(
            constraints: BoxConstraints(
              minWidth: MediaQuery.of(context).size.width,
              maxHeight: ScreenUtil().screenHeight / 1.2,
            ),
            child: Column(
              children: [
                BottomSheetHeaderRow(header: MyStrings.selectBankAccount),
                spaceDown(Dimensions.space10),
                CustomCompanyListTileCard(
                  padding: EdgeInsetsDirectional.symmetric(
                    vertical: Dimensions.space10.w,
                    horizontal: Dimensions.space10.w,
                  ),
                  leading: CustomAppCard(
                    width: Dimensions.space45.w,
                    height: Dimensions.space45.w,
                    radius: Dimensions.largeRadius.r,
                    padding: EdgeInsetsDirectional.zero,
                    child: MyNetworkImageWidget(
                      radius: Dimensions.largeRadius.r,
                      boxFit: BoxFit.scaleDown,
                      imageUrl: bank.getBankImageUrl() ?? "",
                      isProfile: false,
                      width: Dimensions.space40.w,
                      height: Dimensions.space40.w,
                    ),
                  ),
                  showBorder: false,
                  imagePath: bank.getBankImageUrl() ?? "",
                  title: "${bank.name}",
                ),
                spaceDown(Dimensions.space10),
                Container(
                  height: 1,
                  width: double.infinity,
                  color: MyColor.getBorderColor(),
                  margin: const EdgeInsets.symmetric(horizontal: 10),
                ),
                spaceDown(Dimensions.space10),
                if (dataList.isNotEmpty) ...[
                  Expanded(
                    child: SingleChildScrollView(
                      child: Column(
                        children: List.generate(dataList.length, (index) {
                          var item = dataList[index];
                          bool isLastIndex = index == dataList.length - 1;
                          return CustomListTileCard(
                            padding: EdgeInsets.symmetric(
                              vertical: Dimensions.space10.h,
                              horizontal: Dimensions.space20.w,
                            ),
                            leading: CustomAppCard(
                              width: Dimensions.space40.w,
                              height: Dimensions.space40.w,
                              radius: Dimensions.largeRadius.r,
                              padding: EdgeInsetsDirectional.all(
                                Dimensions.space4.w,
                              ),
                              child: MyNetworkImageWidget(
                                boxFit: BoxFit.scaleDown,
                                imageUrl: item.bank?.getBankImageUrl() ?? "",
                                isProfile: false,
                                radius: Dimensions.largeRadius.r,
                                width: Dimensions.space30.w,
                                height: Dimensions.space30.w,
                              ),
                            ),
                            subtitleStyle: MyTextStyle.sectionSubTitle1.copyWith(color: MyColor.getBodyTextColor()),
                            imagePath: "",
                            title: item.accountHolder ?? "",
                            subtitle: (item.accountNumber ?? "").toNumberMask(
                              unmaskedPrefix: 2,
                              unmaskedSuffix: 3,
                              maskChar: "•",
                            ),
                            showBorder: !isLastIndex,
                            trailing: IconButton(
                              onPressed: () {
                                if (controller.isDeleteSaveBankIDLoading == "-1") {
                                  controller.deleteBankAccount(
                                    bankAccountID: item.id?.toString() ?? "",
                                  );
                                }
                              },
                              icon: (controller.isDeleteSaveBankIDLoading == item.id?.toString())
                                  ? SizedBox(
                                      width: Dimensions.space20.w,
                                      height: Dimensions.space20.h,
                                      child: CircularProgressIndicator(
                                        strokeWidth: 2,
                                        color: MyColor.getPrimaryColor(),
                                      ),
                                    )
                                  : Icon(
                                      Icons.close,
                                      color: MyColor.redLightColor,
                                    ),
                            ),
                            onPressed: () {
                              controller.selectBankAccount(item);
                              controller.selectedBankDynamicFormAutofillDataOnTap(
                                item.userData ?? [],
                              );

                              onSavedSuccessCallback();
                              Get.back();
                            },
                          );
                        }),
                      ),
                    ),
                  ),
                ] else ...[
                  Expanded(
                    child: Padding(
                      padding: EdgeInsets.all(Dimensions.space10),
                      child: Row(
                        children: [
                          Icon(
                            Icons.link,
                            color: MyColor.getPrimaryColor(),
                            size: Dimensions.space30.w,
                          ),
                          spaceSide(Dimensions.space10),
                          Expanded(
                            child: Text(
                              MyStrings.addBankAccountMsg.tr,
                              style: MyTextStyle.sectionSubTitle1.copyWith(
                                color: MyColor.getBodyTextColor(),
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ],
                spaceDown(Dimensions.space20),
                CustomElevatedBtn(
                  radius: Dimensions.largeRadius.r,
                  borderColor: MyColor.getPrimaryColor(),
                  bgColor: MyColor.getWhiteColor(),
                  textColor: MyColor.getPrimaryColor(),
                  text: MyStrings.transferNow,
                  icon: Icon(Icons.send, color: MyColor.getPrimaryColor()),
                  onTap: () {
                    controller.clearTextEditingControllers();
                    controller.selectBankAccount(null);
                    controller.selectedBankDynamicFormAutofillDataOnTap([]);
                    onSavedSuccessCallback();
                    Get.back();
                  },
                ),
                spaceDown(Dimensions.space10),
                CustomElevatedBtn(
                  radius: Dimensions.largeRadius.r,
                  bgColor: MyColor.getPrimaryColor(),
                  text: MyStrings.addBankAccount,
                  icon: Icon(Icons.add, color: MyColor.getWhiteColor()),
                  onTap: () {
                    Get.back();
                    controller.clearTextEditingControllers();
                    Get.toNamed(RouteHelper.bankTransferAddNewBankScreen)?.then(
                      (v) {
                        controller.initController(forceLoad: false);
                      },
                    );
                  },
                ),
                spaceDown(Dimensions.space20),
              ],
            ),
          );
        },
      ),
    );
  }
}
