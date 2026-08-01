import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/custom_list_tile_card.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/app/components/image/my_network_image_widget.dart';
import 'package:ovopay/app/components/no_data.dart';
import 'package:ovopay/app/components/shimmer/list_tile_shimmer.dart';
import 'package:ovopay/app/components/text-field/rounded_text_field.dart';
import 'package:ovopay/app/components/text/header_text.dart';
import 'package:ovopay/app/screens/micro_finance_screen/controller/micro_finance_controller.dart';
import 'package:skeletonizer/skeletonizer.dart';

import '../../../../../core/utils/util_exporter.dart';

class MicroFinanceSelectOrganizationTypePageWidget extends StatelessWidget {
  const MicroFinanceSelectOrganizationTypePageWidget({
    super.key,
    required this.context,
    required this.onSuccessCallback,
  });
  final VoidCallback onSuccessCallback;
  final BuildContext context;
  @override
  Widget build(BuildContext context) {
    return GetBuilder<MicroFinanceController>(
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
                                controller: controller.organizationNameController,
                                labelText: MyStrings.enterOrganizationNameOrType.tr,
                                hintText: MyStrings.enterOrganizationNameOrType.tr,
                                textInputAction: TextInputAction.search,
                                keyboardType: TextInputType.text,
                                suffixIcon: IconButton(
                                  onPressed: null,
                                  icon: MyAssetImageWidget(
                                    color: MyColor.getPrimaryColor(),
                                    width: Dimensions.space24.w,
                                    height: Dimensions.space24.w,
                                    boxFit: BoxFit.contain,
                                    assetPath: MyIcons.searchIcon,
                                    isSvg: true,
                                  ),
                                ),
                                onChanged: (value) {
                                  controller.filterNgoListName(value);
                                },
                              ),
                              if (controller.organizationNameController.text.isEmptyString && controller.latestHistory.isNotEmpty) ...[
                                spaceDown(Dimensions.space16),
                                HeaderText(
                                  text: MyStrings.recentlyDonatedOrganization.tr,
                                  textStyle: MyTextStyle.sectionTitle2.copyWith(
                                    color: MyColor.getHeaderTextColor(),
                                  ),
                                ),
                                spaceDown(Dimensions.space8),
                                ...List.generate(
                                  controller.latestHistory.length,
                                  (index) {
                                    var item = controller.latestHistory[index];
                                    bool isLastIndex = index == controller.latestHistory.length - 1;
                                    return CustomListTileCard(
                                      padding: EdgeInsets.symmetric(
                                        vertical: Dimensions.space20.h,
                                      ),
                                      leading: CustomAppCard(
                                        width: Dimensions.space45.w,
                                        height: Dimensions.space45.w,
                                        radius: Dimensions.badgeRadius,
                                        padding: EdgeInsetsDirectional.all(
                                          Dimensions.space4.w,
                                        ),
                                        child: MyNetworkImageWidget(
                                          boxFit: BoxFit.scaleDown,
                                          imageUrl: item.ngo?.getNgoImageUrl() ?? "",
                                          isProfile: false,
                                          width: Dimensions.space40.w,
                                          height: Dimensions.space40.w,
                                        ),
                                      ),
                                      imagePath: "",
                                      title: item.ngo?.name ?? "",
                                      showBorder: !isLastIndex,
                                      onPressed: () {
                                        controller.selectedNgByTappingTransactionOnTap(
                                          item,
                                        );
                                        onSuccessCallback();
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
              var dataList = controller.filterNgoListDataList;
              if (controller.isPageLoading) {
                return ListTileShimmer();
              }
              if (dataList.isEmpty) {
                return SingleChildScrollView(
                  child: CustomAppCard(
                    child: NoDataWidget(
                      text: MyStrings.noMatchingInstituteFound.tr,
                    ),
                  ),
                );
              }

              return CustomAppCard(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    HeaderText(
                      text: MyStrings.allOrganization.tr,
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
                                  radius: Dimensions.badgeRadius,
                                  padding: EdgeInsetsDirectional.all(
                                    Dimensions.space4.w,
                                  ),
                                  child: MyNetworkImageWidget(
                                    boxFit: BoxFit.scaleDown,
                                    imageUrl: item.getNgoImageUrl() ?? "",
                                    isProfile: false,
                                    width: Dimensions.space40.w,
                                    height: Dimensions.space40.w,
                                  ),
                                ),
                                imagePath: "",
                                title: "${item.name}",
                                showBorder: !isLastIndex,
                                onPressed: () {
                                  controller.selectedNgoOnTap(item);
                                  onSuccessCallback();
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
}
