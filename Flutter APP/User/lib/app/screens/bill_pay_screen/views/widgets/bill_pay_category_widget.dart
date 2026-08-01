import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/image/my_network_image_widget.dart';
import 'package:ovopay/app/components/shimmer/grid_category_shimmer.dart';
import 'package:ovopay/app/components/text/header_text.dart';
import 'package:ovopay/app/packages/auto_height_grid_view/auto_height_grid_view.dart';
import 'package:ovopay/app/screens/bill_pay_screen/controller/bill_pay_controller.dart';
import 'package:ovopay/core/utils/util_exporter.dart';

class BillPayCategoryWidgetCard extends StatefulWidget {
  const BillPayCategoryWidgetCard({super.key});

  @override
  State<BillPayCategoryWidgetCard> createState() => _BillPayCategoryWidgetCardState();
}

class _BillPayCategoryWidgetCardState extends State<BillPayCategoryWidgetCard> {
  @override
  Widget build(BuildContext context) {
    return GetBuilder<BillPayController>(
      builder: (billPayController) {
        return CustomAppCard(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              HeaderText(
                text: MyStrings.billType.tr,
                textStyle: MyTextStyle.sectionTitle2.copyWith(
                  color: MyColor.getHeaderTextColor(),
                ),
              ),
              spaceDown(Dimensions.space16),
              if (billPayController.isPageLoading) ...[
                GridCategoryShimmer(),
              ] else ...[
                //Action Icons
                AutoHeightGridView(
                  physics: NeverScrollableScrollPhysics(),
                  padding: EdgeInsets.zero,
                  shrinkWrap: true,
                  itemCount: billPayController.utilityCategoryDataList.length,
                  crossAxisCount: 4,
                  builder: (context, index) {
                    var item = billPayController.utilityCategoryDataList[index];
                    return buildServiceItem(
                      BillMenuItem(
                        icon: item.getCategoryImageUrl() ?? "",
                        label: item.formattedName ?? "",
                        isActive: billPayController.selectedUtilityCategory?.id == item.id,
                        activeColor: MyColor.white,
                        onTap: () {
                          billPayController.selectedUtilityCategoryOnTap(item);
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

  Widget buildServiceItem(BillMenuItem service) {
    return GestureDetector(
      onTap: service.onTap,
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          CustomAppCard(
            backgroundColor: service.activeColor.withValues(alpha: 0.1),
            borderColor: service.isActive == true ? MyColor.getPrimaryColor() : MyColor.getBorderColor(),
            padding: EdgeInsets.all(Dimensions.space5.w),
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
              overflow: TextOverflow.visible,
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

class BillMenuItem {
  final String icon;
  final String label;
  final bool isActive;
  final Color activeColor;
  final VoidCallback? onTap;

  BillMenuItem({
    this.onTap,
    required this.icon,
    required this.label,
    this.isActive = false,
    required this.activeColor,
  });
}
