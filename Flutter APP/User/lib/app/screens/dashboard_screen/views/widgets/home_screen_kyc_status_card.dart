import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/screens/dashboard_screen/controller/home_controller.dart';
import 'package:ovopay/core/data/services/service_exporter.dart';
import 'package:ovopay/core/route/route.dart';
import 'package:ovopay/core/utils/util_exporter.dart';

class HomeScreenKycStatusCard extends StatelessWidget {
  const HomeScreenKycStatusCard({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<HomeController>(
      builder: (homeController) {
        final kycStatus = homeController.kycStatus;

        // Get colors based on status
        final Color borderColor, bgColor, iconColor, titleColor, subtitleColor;
        final String title, subtitle;
        switch (kycStatus) {
          case AppStatus.KYC_PENDING:
            title = MyStrings.kycVerificationPending.tr;
            subtitle = (SharedPreferenceService.kycContent()?.dataValues?.pending ?? MyStrings.kycVerificationPendingMSg).tr;
            borderColor = MyColor.warning;
            bgColor = MyColor.warning.withValues(alpha: 0.05);
            iconColor = MyColor.warning;
            titleColor = MyColor.getBodyTextColor();
            subtitleColor = MyColor.getBodyTextColor().withValues(alpha: 0.7);
            break;
          case AppStatus.KYC_REJECTED:
            title = MyStrings.kycVerificationRejected.tr;
            subtitle = (SharedPreferenceService.kycContent()?.dataValues?.reject ?? MyStrings.kycVerificationRejectedMSg).tr;
            borderColor = MyColor.redLightColor;
            bgColor = MyColor.redLightColor.withValues(alpha: 0.05);
            iconColor = MyColor.redLightColor;
            titleColor = MyColor.redLightColor;
            subtitleColor = MyColor.getBodyTextColor();
            break;
          default:
            title = MyStrings.kycVerificationRequired.tr;
            subtitle = (SharedPreferenceService.kycContent()?.dataValues?.required ?? MyStrings.kycVerificationMsg).tr;
            borderColor = MyColor.statusColor;
            bgColor = MyColor.statusColor.withValues(alpha: 0.05);
            iconColor = MyColor.statusColor;
            titleColor = MyColor.getHeaderTextColor();
            subtitleColor = MyColor.getBodyTextColor();
            break;
        }

        return kycStatus == AppStatus.KYC_APPROVED
            ? SizedBox()
            : CustomAppCard(
                onPressed: () => Get.toNamed(RouteHelper.kycScreen),
                borderColor: borderColor,
                backgroundColor: bgColor,
                width: double.infinity,
                margin: EdgeInsetsDirectional.only(bottom: Dimensions.space10),
                child: Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    if (kycStatus == AppStatus.KYC_REJECTED) ...[
                      Icon(Icons.close_rounded, color: iconColor),
                    ] else ...[
                      Icon(Icons.info_outlined, color: iconColor),
                    ],
                    spaceSide(Dimensions.space10),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            title,
                            style: MyTextStyle.sectionTitle3.copyWith(
                              color: titleColor,
                            ),
                          ),
                          spaceDown(Dimensions.space2),
                          Text(
                            subtitle,
                            style: MyTextStyle.caption2Style.copyWith(
                              color: subtitleColor,
                            ),
                          ),
                          if (kycStatus == AppStatus.KYC_REJECTED) ...[
                            spaceDown(Dimensions.space2),
                            RichText(
                              text: TextSpan(
                                children: [
                                  TextSpan(
                                    text: "${MyStrings.reason.tr} : ",
                                    style: MyTextStyle.caption2Style.copyWith(
                                      color: MyColor.redLightColor,
                                    ),
                                  ),
                                  TextSpan(
                                    text: homeController.kycReason,
                                    style: MyTextStyle.caption2Style.copyWith(
                                      color: MyColor.redLightColor,
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ],
                        ],
                      ),
                    ),
                  ],
                ),
              );
      },
    );
  }
}
