import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/bottom-sheet/bottom_sheet_header_row.dart';
import 'package:ovopay/app/components/bottom-sheet/custom_bottom_sheet_plus.dart';
import 'package:ovopay/app/components/buttons/custom_elevated_button.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/image/my_network_image_widget.dart';
import 'package:ovopay/app/components/text/header_text.dart';
import 'package:ovopay/app/packages/auto_height_grid_view/auto_height_grid_view.dart';
import 'package:ovopay/app/screens/dashboard_screen/controller/home_controller.dart';
import 'package:ovopay/core/data/models/home/offers_response_model.dart';
import 'package:ovopay/core/route/route.dart';
import 'package:ovopay/core/utils/util_exporter.dart';

class HomeScreenPaymentOffersCard extends StatelessWidget {
  const HomeScreenPaymentOffersCard({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<HomeController>(
      builder: (homeController) {
        return homeController.offersList.isEmpty
            ? SizedBox.shrink()
            : CustomAppCard(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      crossAxisAlignment: CrossAxisAlignment.center,
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        HeaderText(
                          text: MyStrings.paymentOffers.tr,
                          textStyle: MyTextStyle.sectionTitle2.copyWith(
                            color: MyColor.getHeaderTextColor(),
                          ),
                        ),
                        Material(
                          type: MaterialType.transparency,
                          borderRadius: BorderRadius.circular(
                            Dimensions.largeRadius.r,
                          ),
                          child: InkWell(
                            borderRadius: BorderRadius.circular(
                              Dimensions.largeRadius.r,
                            ),
                            onTap: () {
                              Get.toNamed(RouteHelper.promotionalOffersScreen);
                            },
                            child: Padding(
                              padding: EdgeInsets.symmetric(
                                horizontal: Dimensions.space3.w,
                                vertical: Dimensions.space2.w,
                              ),
                              child: Text(
                                MyStrings.showAll.tr,
                                style: MyTextStyle.appBarActionButtonTextStyleTitle.copyWith(decoration: TextDecoration.none),
                              ),
                            ),
                          ),
                        ),
                      ],
                    ),
                    spaceDown(Dimensions.space16),
                    AutoHeightGridView(
                      itemCount: homeController.offersList.length,
                      crossAxisCount: 3,
                      mainAxisSpacing: Dimensions.space12.w,
                      crossAxisSpacing: Dimensions.space12.w,
                      physics: const NeverScrollableScrollPhysics(),
                      padding: EdgeInsets.zero,
                      shrinkWrap: true,
                      builder: (context, index) {
                        var item = homeController.offersList[index];
                        return CustomAppCard(
                          padding: EdgeInsets.all(Dimensions.space12.w),
                          radius: Dimensions.largeRadius.r,
                          child: Column(
                            mainAxisAlignment: MainAxisAlignment.start,
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              MyNetworkImageWidget(
                                imageUrl: item.getOfferImageUrl() ?? "",
                                boxFit: BoxFit.contain,
                                width: double.infinity,
                                height: Dimensions.space60.h,
                              ),
                              spaceDown(Dimensions.space4),
                              Text(
                                item.discountType == "1" ? "${MyUtils.getUserAmount(item.amount ?? "0", precision: 0)} ${MyStrings.discount.tr}" : "${MyUtils.getUserAmount(item.amount ?? "0", precision: 0, showPercent: true)} ${MyStrings.off.tr}",
                                textAlign: TextAlign.start,
                                overflow: TextOverflow.ellipsis,
                                style: MyTextStyle.caption1Style.copyWith(
                                  fontWeight: FontWeight.w600,
                                  color: MyColor.error,
                                ),
                              ),
                              spaceDown(Dimensions.space4),
                              Text(
                                "${item.merchant?.getFullName()}",
                                textAlign: TextAlign.start,
                                overflow: TextOverflow.visible,
                                style: MyTextStyle.caption1Style.copyWith(
                                  color: MyColor.getBodyTextColor(),
                                ),
                              ),
                            ],
                          ),
                          onPressed: () {
                            CustomBottomSheetPlus(
                              child: SafeArea(
                                child: buildDetailsSectionSheet(
                                  item: item,
                                  context: context,
                                ),
                              ),
                            ).show(context);
                          },
                        );
                      },
                    ),
                  ],
                ),
              );
      },
    );
  }

  Widget buildDetailsSectionSheet({
    required OfferModel item,
    required BuildContext context,
  }) {
    return SingleChildScrollView(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          BottomSheetHeaderRow(header: MyStrings.details),
          spaceDown(Dimensions.space10),
          MyNetworkImageWidget(
            imageUrl: item.getOfferImageUrl() ?? "",
            boxFit: BoxFit.contain,
            width: double.infinity,
            height: Dimensions.space100.h * 1.5,
          ),
          spaceDown(Dimensions.space10),
          Text(
            item.discountType == "1" ? "${MyUtils.getUserAmount(item.amount ?? "0", precision: 0)} ${MyStrings.discount.tr}" : "${MyUtils.getUserAmount(item.amount ?? "0", precision: 0, showPercent: true)} ${MyStrings.off.tr}",
            textAlign: TextAlign.start,
            overflow: TextOverflow.ellipsis,
            style: MyTextStyle.sectionTitle.copyWith(
              fontWeight: FontWeight.w600,
              color: MyColor.error,
            ),
          ),
          spaceDown(Dimensions.space4),
          Text(
            "${item.merchant?.getFullName()}",
            textAlign: TextAlign.start,
            overflow: TextOverflow.visible,
            style: MyTextStyle.sectionTitle.copyWith(
              color: MyColor.getBodyTextColor(),
            ),
          ),
          spaceDown(Dimensions.space4),
          Text(
            item.description ?? "",
            textAlign: TextAlign.start,
            overflow: TextOverflow.visible,
            style: MyTextStyle.sectionTitle.copyWith(
              fontWeight: FontWeight.normal,
              color: MyColor.getBodyTextColor(),
            ),
          ),
          spaceDown(Dimensions.space4),
          if (item.link != null) ...[
            Align(
              alignment: AlignmentDirectional.centerEnd,
              child: CustomElevatedBtn(
                width: Dimensions.space80.w,
                height: Dimensions.space40.h,
                text: MyStrings.more,
                icon: Icon(
                  Icons.arrow_forward_ios_rounded,
                  color: MyColor.getWhiteColor(),
                ),
                iconAlignment: IconAlignment.end,
                onTap: () {
                  MyUtils.launchUrlToBrowser(item.link ?? "");
                },
              ),
            ),
          ],
        ],
      ),
    );
  }
}
