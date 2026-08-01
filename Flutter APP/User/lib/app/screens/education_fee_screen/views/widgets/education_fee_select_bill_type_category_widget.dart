import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/image/my_network_image_widget.dart';
import 'package:ovopay/app/components/shimmer/grid_category_shimmer.dart';
import 'package:ovopay/app/components/text/header_text.dart';
import 'package:ovopay/app/screens/education_fee_screen/controller/education_fee_controller.dart';
import 'package:ovopay/core/utils/util_exporter.dart';

class EducationFeeCategoryWidgetCard extends StatefulWidget {
  const EducationFeeCategoryWidgetCard({super.key});

  @override
  State<EducationFeeCategoryWidgetCard> createState() => _EducationFeeCategoryWidgetCardState();
}

class _EducationFeeCategoryWidgetCardState extends State<EducationFeeCategoryWidgetCard> {
  @override
  Widget build(BuildContext context) {
    return GetBuilder<EducationFeeController>(
      builder: (educationController) {
        return CustomAppCard(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              HeaderText(
                text: MyStrings.instituteAndFeeType.tr,
                textStyle: MyTextStyle.sectionTitle2.copyWith(
                  color: MyColor.getHeaderTextColor(),
                ),
              ),
              spaceDown(Dimensions.space16),
              if (educationController.isPageLoading) ...[
                GridCategoryShimmer(),
              ] else ...[
                //Action Icons
                GridView.builder(
                  padding: EdgeInsets.zero,
                  shrinkWrap: true,
                  itemCount: educationController.educationCategoryDataList.length,
                  gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
                    crossAxisCount: 4,
                  ),
                  itemBuilder: (context, index) {
                    var item = educationController.educationCategoryDataList[index];
                    return buildServiceItem(
                      InstituteCategoryMenuItem(
                        icon: item.getCategoryImageUrl() ?? "",
                        label: item.name ?? "",
                        isActive: educationController.selectedEducationCategory?.id == item.id,
                        activeColor: MyColor.white,
                        onTap: () {
                          educationController.selectEducationCategoryOnTap(
                            item,
                          );
                        },
                      ),
                    );
                  },
                ),
              ],
            ],
          ),
        );
      },
    );
  }

  Widget buildServiceItem(InstituteCategoryMenuItem service) {
    return GestureDetector(
      onTap: service.onTap,
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          CustomAppCard(
            backgroundColor: service.activeColor.withValues(alpha: 0.1),
            borderColor: service.isActive == true ? MyColor.getPrimaryColor() : MyColor.getBorderColor(),
            padding: EdgeInsets.all(Dimensions.space12.w),
            width: Dimensions.space48.w,
            height: Dimensions.space48.w,
            radius: Dimensions.largeRadius.r,
            child: MyNetworkImageWidget(
              imageUrl: service.icon,
              width: Dimensions.space24.w,
              height: Dimensions.space24.w,
              // color: service.activeColor,
            ),
          ),
          spaceDown(Dimensions.space4),
          Flexible(
            child: Text(
              service.label.tr,
              textAlign: TextAlign.center,
              overflow: TextOverflow.ellipsis,
              style: MyTextStyle.caption1Style.copyWith(
                color: MyColor.getBodyTextColor(),
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class InstituteCategoryMenuItem {
  final String icon;
  final String label;
  final bool isActive;
  final Color activeColor;
  final VoidCallback? onTap;

  InstituteCategoryMenuItem({
    this.onTap,
    required this.icon,
    required this.label,
    this.isActive = false,
    required this.activeColor,
  });
}
