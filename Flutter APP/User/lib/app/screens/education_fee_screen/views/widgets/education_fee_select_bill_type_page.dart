import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/custom_company_list_tile_card.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/app/components/no_data.dart';
import 'package:ovopay/app/components/shimmer/list_tile_shimmer.dart';
import 'package:ovopay/app/components/text-field/rounded_text_field.dart';
import 'package:ovopay/app/components/text/header_text.dart';
import 'package:ovopay/app/screens/education_fee_screen/controller/education_fee_controller.dart';
import 'package:ovopay/app/screens/education_fee_screen/views/widgets/education_fee_select_bill_type_category_widget.dart';
import 'package:skeletonizer/skeletonizer.dart';

import '../../../../../core/utils/util_exporter.dart';

class EducationFeeSelectAndSearchInstitutePageWidget extends StatelessWidget {
  final BuildContext context;
  const EducationFeeSelectAndSearchInstitutePageWidget({
    super.key,
    required this.context,
    required this.onSuccessCallback,
  });
  final VoidCallback onSuccessCallback;
  @override
  Widget build(BuildContext context) {
    return GetBuilder<EducationFeeController>(
      builder: (billPayController) {
        return NestedScrollView(
          headerSliverBuilder: (BuildContext context, bool innerBoxIsScrolled) {
            return <Widget>[
              SliverToBoxAdapter(
                child: Skeletonizer(
                  enabled: billPayController.isPageLoading,
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
                                text: MyStrings.searchInstitute.tr,
                                textStyle: MyTextStyle.sectionTitle.copyWith(
                                  color: MyColor.getHeaderTextColor(),
                                ),
                              ),
                              spaceDown(Dimensions.space8),
                              RoundedTextField(
                                controller: billPayController.instituteNameController,
                                showLabelText: false,
                                labelText: MyStrings.enterInstituteNameOrType.tr,
                                hintText: MyStrings.enterInstituteNameOrType.tr,
                                textInputAction: TextInputAction.done,
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
                                  billPayController.filterEducationInstitute(
                                    value,
                                  );
                                },
                              ),
                            ],
                          ),
                        ),
                        if (billPayController.instituteNameController.text.trim().isEmpty) ...[
                          spaceDown(Dimensions.space16),
                          EducationFeeCategoryWidgetCard(),
                        ],
                      ],
                    ),
                  ),
                ),
              ),
            ];
          },
          body: Builder(
            builder: (context) {
              var isCategorySelected = billPayController.selectedEducationCategory != null;
              var dataList = isCategorySelected ? billPayController.selectedEducationCategory?.institute ?? [] : billPayController.filterEducationInstituteDataList;
              if (billPayController.isPageLoading) {
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
                      text: MyStrings.allInstitute.tr,
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
                              return CustomCompanyListTileCard(
                                onPressed: () {
                                  billPayController.selectAnEducationInstituteOnTap(item);
                                  onSuccessCallback();
                                },
                                imagePath: item.getInstituteImageUrl() ?? "",
                                title: "${item.name}",
                                subtitle: "${billPayController.educationCategoryDataList.firstWhere((e) => e.id?.toString() == item.categoryId?.toString()).name}",
                                showBorder: !isLastIndex,
                                titleStyle: MyTextStyle.sectionSubTitle1.copyWith(fontWeight: FontWeight.w600),
                                subtitleStyle: MyTextStyle.caption2Style.copyWith(
                                  color: MyColor.getBodyTextColor(),
                                ),
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
