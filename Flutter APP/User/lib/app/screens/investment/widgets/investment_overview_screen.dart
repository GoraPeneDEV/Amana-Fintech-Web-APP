import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/buttons/custom_elevated_button.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/divider/dashed_divider.dart';
import 'package:ovopay/app/components/no_data.dart';
import 'package:ovopay/app/components/shimmer/investment_plan_shimmer.dart';
import 'package:ovopay/app/screens/investment/controller/investment_controller.dart';
import 'package:ovopay/core/data/services/shared_pref_service.dart';
import 'package:ovopay/core/utils/util_exporter.dart';

class InvestmentOverviewScreen extends StatelessWidget {
  final VoidCallback? onNextTap;
  final ScrollController scrollController;
  const InvestmentOverviewScreen({super.key, this.onNextTap, required this.scrollController});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<InvestmentController>(
      builder: (controller) {
        return controller.isPlanLoading && controller.investmentPlanList.isEmpty
            ? InvestmentPlanShimmer()
            : (controller.investmentPlanList.isEmpty)
                ? SingleChildScrollView(
                    physics: AlwaysScrollableScrollPhysics(),
                    child: NoDataWidget(
                      text: MyStrings.noDataToShow.tr,
                    ),
                  )
                : RefreshIndicator(
                    color: MyColor.getPrimaryColor(),
                    onRefresh: () async {
                      await controller.initialPlanData();
                    },
                    child: ListView.separated(
                        controller: scrollController,
                        separatorBuilder: (context, index) => spaceDown(Dimensions.space10),
                        padding: EdgeInsets.zero,
                        itemCount: controller.hasNext() ? controller.investmentPlanList.length + 1 : controller.investmentPlanList.length,
                        itemBuilder: (BuildContext context, int index) {
                          if (index == controller.investmentPlanList.length) {
                            return Padding(
                              padding: const EdgeInsets.all(10.0),
                              child: Center(
                                child: CircularProgressIndicator(
                                  color: MyColor.getPrimaryColor(),
                                ),
                              ),
                            );
                          }

                          var planData = controller.investmentPlanList[index];
                          if (!controller.sliderValues.containsKey(index)) {
                            if (planData.investType == "0") {
                              double minValue = double.tryParse(planData.minInvest ?? "0") ?? 0.0;
                              double maxValue = double.tryParse(planData.maxInvest ?? "0") ?? 0.0;
                              controller.sliderValues[index] = (minValue + maxValue) / 2;
                            } else {
                              controller.sliderValues[index] = double.tryParse(planData.fixedAmount ?? "0") ?? 0.0;
                            }
                          }
                          double fixedAmount = double.tryParse(planData.fixedAmount ?? "0") ?? 0.0;
                          controller.calculateReturns(index, double.tryParse(controller.sliderValues[index].toString()) ?? 0.0);

                          return CustomAppCard(
                              child: ExpansionTile(
                            key: Key(index.toString()),
                            initiallyExpanded: controller.selectedPlanIndex == index,
                            shape: Border(),
                            tilePadding: EdgeInsets.zero,
                            childrenPadding: EdgeInsets.zero,
                            trailing: CustomAppCard(
                              radius: Dimensions.space100,
                              padding: EdgeInsets.all(Dimensions.space4),
                              child: Icon(
                                controller.selectedPlanIndex == index ? Icons.keyboard_arrow_up_rounded : Icons.keyboard_arrow_down_rounded,
                                color: MyColor.getPrimaryColor(),
                                size: 24,
                              ),
                            ),
                            onExpansionChanged: (expanded) {
                              controller.updateExpandIndex(index, expanded);
                            },
                            title: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  planData.name ?? "",
                                  style: MyTextStyle.sectionTitle.copyWith(
                                    color: MyColor.getHeaderTextColor(),
                                  ),
                                ),
                                spaceDown(Dimensions.space4),
                                Text(
                                  planData.description ?? "",
                                  style: MyTextStyle.sectionBodyTextStyle.copyWith(
                                    color: MyColor.getBodyTextColor(),
                                  ),
                                ),
                              ],
                            ),
                            children: [
                              Column(
                                children: [
                                  spaceDown(Dimensions.space20),
                                  CustomAppCard(
                                    padding: EdgeInsets.symmetric(vertical: Dimensions.space10, horizontal: Dimensions.space8),
                                    radius: Dimensions.space8,
                                    child: Column(
                                      children: [
                                        Row(
                                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                          children: [
                                            Flexible(
                                              child: Text(
                                                MyStrings.investmentAmount.tr,
                                                style: MyTextStyle.sectionBodyTextStyle.copyWith(
                                                  color: MyColor.getHeaderTextColor(),
                                                ),
                                              ),
                                            ),
                                            Flexible(
                                              child: Text(
                                                planData.investType == "0" ? "${AppConverter.formatNumber(planData.minInvest ?? "")} - ${AppConverter.formatNumber(planData.maxInvest ?? "")} ${SharedPreferenceService.getCurrencySymbol().toLowerCase()}" : "${AppConverter.formatNumber(planData.fixedAmount ?? "")} ${SharedPreferenceService.getCurrencySymbol().toLowerCase()} ${MyStrings.fixed.tr}",
                                                style: MyTextStyle.sectionBodyTextStyle.copyWith(color: MyColor.getHeaderTextColor(), fontWeight: FontWeight.w600),
                                              ),
                                            ),
                                          ],
                                        ),
                                        SizedBox(height: Dimensions.space15),
                                        Row(
                                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                          children: [
                                            Text(
                                              MyStrings.selectedAmount.tr,
                                              style: MyTextStyle.sectionBodyTextStyle.copyWith(
                                                color: MyColor.getHeaderTextColor(),
                                              ),
                                            ),
                                            Text(
                                              "${AppConverter.formatNumber(controller.sliderValues[index]?.toStringAsFixed(2) ?? "0")} ${SharedPreferenceService.getCurrencySymbol().toLowerCase()}",
                                              style: MyTextStyle.sectionBodyTextStyle.copyWith(
                                                color: MyColor.getPrimaryColor(),
                                                fontWeight: FontWeight.w600,
                                              ),
                                            ),
                                          ],
                                        ),
                                        SizedBox(height: Dimensions.space10),
                                        SliderTheme(
                                            data: SliderTheme.of(context).copyWith(
                                                activeTrackColor: MyColor.getPrimaryColor(),
                                                inactiveTrackColor: MyColor.getDarkColor().withValues(alpha: .1),
                                                thumbColor: MyColor.white,
                                                thumbShape: const RoundSliderThumbShape(enabledThumbRadius: 12),
                                                overlayColor: MyColor.getPrimaryColor().withValues(alpha: .2),
                                                overlayShape: const RoundSliderOverlayShape(
                                                  overlayRadius: 24.0,
                                                ),
                                                trackHeight: 4.0,
                                                trackShape: const RoundedRectSliderTrackShape(),
                                                padding: EdgeInsets.symmetric(horizontal: Dimensions.space8, vertical: Dimensions.space12)),
                                            child: Slider(
                                              allowedInteraction: SliderInteraction.slideOnly,
                                              value: planData.investType == "1"
                                                  ? fixedAmount
                                                  : (controller.sliderValues[index] ?? 0.0).clamp(
                                                      double.tryParse(planData.minInvest ?? "0") ?? 0.0,
                                                      double.tryParse(planData.maxInvest ?? "100") ?? 100.0,
                                                    ),
                                              min: planData.investType == "1" ? 0 : double.tryParse(planData.minInvest ?? "0") ?? 0.0,
                                              max: planData.investType == "1" ? fixedAmount : double.tryParse(planData.maxInvest ?? "100") ?? 100.0,
                                              onChanged: planData.investType == "1"
                                                  ? null
                                                  : (value) {
                                                      controller.updateSliderValue(index, value);
                                                    },
                                            )),
                                      ],
                                    ),
                                  ),
                                  spaceDown(Dimensions.space20),
                                  CustomAppCard(
                                      padding: EdgeInsets.symmetric(vertical: Dimensions.space10, horizontal: Dimensions.space8),
                                      radius: Dimensions.space8,
                                      width: double.infinity,
                                      showBorder: false,
                                      backgroundColor: MyColor.primary.withValues(alpha: .15),
                                      child: Column(
                                        crossAxisAlignment: CrossAxisAlignment.center,
                                        mainAxisAlignment: MainAxisAlignment.center,
                                        children: [
                                          Text(
                                            "${AppConverter.formatNumber(
                                              planData.interestAmount ?? "",
                                              precision: planData.interestType == "0" ? 0 : 2,
                                            )}${planData.interestType == "0" ? "%" : " ${SharedPreferenceService.getCurrencySymbol().toLowerCase()}"}",
                                            textAlign: TextAlign.center,
                                            style: MyTextStyle.sectionTitle.copyWith(
                                              color: MyColor.getPrimaryColor(),
                                            ),
                                          ),
                                          spaceDown(Dimensions.space4),
                                          Text(
                                            planData.interestType != "0" ? MyStrings.interestAmount.tr : MyStrings.interestRate.tr,
                                            textAlign: TextAlign.center,
                                            style: MyTextStyle.sectionSubTitle1.copyWith(
                                              color: MyColor.getHeaderTextColor(),
                                            ),
                                          ),
                                        ],
                                      )),
                                  spaceDown(Dimensions.space10),
                                  IntrinsicHeight(
                                    child: Row(
                                      children: [
                                        Expanded(
                                          child: CustomAppCard(
                                              showBorder: false,
                                              backgroundColor: MyColor.lemonadeColor,
                                              width: double.infinity,
                                              padding: EdgeInsetsDirectional.symmetric(vertical: Dimensions.space10),
                                              radius: Dimensions.space10,
                                              child: Column(
                                                crossAxisAlignment: CrossAxisAlignment.center,
                                                mainAxisAlignment: MainAxisAlignment.center,
                                                children: [
                                                  Text(
                                                    planData.investTime?.name ?? "",
                                                    textAlign: TextAlign.center,
                                                    style: MyTextStyle.sectionTitle2.copyWith(
                                                      color: MyColor.getHeaderTextColor(),
                                                    ),
                                                  ),
                                                  spaceDown(Dimensions.space4),
                                                  Text(
                                                    MyStrings.interestPeriod.tr,
                                                    textAlign: TextAlign.center,
                                                    style: MyTextStyle.sectionSubTitle1.copyWith(
                                                      color: MyColor.getHeaderTextColor(),
                                                    ),
                                                  )
                                                ],
                                              )),
                                        ),
                                        spaceSide(Dimensions.space15),
                                        Expanded(
                                          child: CustomAppCard(
                                              showBorder: false,
                                              backgroundColor: MyColor.beigeColor,
                                              width: double.infinity,
                                              padding: EdgeInsetsDirectional.symmetric(vertical: Dimensions.space10),
                                              radius: Dimensions.space10,
                                              child: Column(
                                                crossAxisAlignment: CrossAxisAlignment.center,
                                                mainAxisAlignment: MainAxisAlignment.center,
                                                children: [
                                                  Text(
                                                    "${planData.repeatTimes ?? ""}x",
                                                    textAlign: TextAlign.center,
                                                    style: MyTextStyle.sectionTitle2.copyWith(
                                                      color: MyColor.getHeaderTextColor(),
                                                    ),
                                                  ),
                                                  spaceDown(Dimensions.space4),
                                                  Text(
                                                    textAlign: TextAlign.center,
                                                    MyStrings.times.tr,
                                                    style: MyTextStyle.sectionSubTitle1.copyWith(
                                                      color: MyColor.getHeaderTextColor(),
                                                    ),
                                                  )
                                                ],
                                              )),
                                        )
                                      ],
                                    ),
                                  ),
                                  spaceDown(Dimensions.space20),
                                  _buildDetailsTile(MyStrings.returnType, planData.returnType == "0" ? MyStrings.lifeTime.tr : MyStrings.repeat.tr, null),
                                  DashedDivider(),
                                  _buildDetailsTile(MyStrings.capitalBack, planData.capitalBack != "0" ? MyStrings.yes.tr : MyStrings.no.tr, planData.capitalBack == "0" ? MyColor.redLightColor : MyColor.greenLightColor),
                                  DashedDivider(),
                                  _buildDetailsTile(
                                    MyStrings.perInterestAmount,
                                    "${AppConverter.formatNumber(controller.perInterestAmount[index]?.toString() ?? "0")} ${SharedPreferenceService.getCurrencySymbol().toLowerCase()}",
                                    null,
                                  ),
                                  DashedDivider(),
                                  _buildDetailsTile(
                                    MyStrings.totalReturn,
                                    planData.returnType == "0" ? MyStrings.unlimited.tr : "${AppConverter.formatNumber(controller.totalReturns[index]?.toString() ?? "0")} ${SharedPreferenceService.getCurrencySymbol().toLowerCase()}",
                                    null,
                                    subTitle: planData.capitalBack != "0" ? MyStrings.withCapital : null,
                                  ),
                                  spaceDown(Dimensions.space15),
                                  CustomElevatedBtn(
                                      text: MyStrings.investNow,
                                      onTap: () {
                                        controller.selectAPlanData(() {
                                          onNextTap?.call();
                                        });
                                      })
                                ],
                              )
                            ],
                          ));
                        }),
                  );
      },
    );
  }

  Widget _buildDetailsTile(String title, String value, Color? textColor, {String? subTitle}) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Flexible(
          child: Text(
            title.tr,
            style: MyTextStyle.bodyTextStyle1.copyWith(
              color: MyColor.getHeaderTextColor(),
            ),
          ),
        ),
        Flexible(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.end,
            mainAxisAlignment: MainAxisAlignment.end,
            children: [
              Text(
                value.tr,
                style: MyTextStyle.bodyTextStyle1.copyWith(color: textColor ?? MyColor.getHeaderTextColor(), fontWeight: FontWeight.w600),
              ),
              if (subTitle != null) ...[
                Text(
                  subTitle.tr,
                  style: MyTextStyle.caption2Style.copyWith(color: MyColor.getBodyTextColor(), fontWeight: FontWeight.w600, fontSize: Dimensions.fontExtraSmall),
                ),
              ],
            ],
          ),
        ),
      ],
    );
  }
}
