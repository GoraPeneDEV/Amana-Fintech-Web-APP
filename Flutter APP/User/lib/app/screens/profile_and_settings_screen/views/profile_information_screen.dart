import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/buttons/custom_elevated_button.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/custom_list_tile_information_widget_card.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/app/screens/profile_and_settings_screen/controller/profile_controller.dart';
import 'package:ovopay/app/screens/profile_and_settings_screen/views/widgets/profile_image_with_upload_button_widget.dart';
import 'package:ovopay/core/data/repositories/account/profile_repo.dart';
import 'package:ovopay/core/route/route.dart';
import 'package:skeletonizer/skeletonizer.dart';

import '../../../../core/data/services/service_exporter.dart';
import '../../../../core/utils/util_exporter.dart';

class ProfileInformationScreen extends StatefulWidget {
  const ProfileInformationScreen({super.key});

  @override
  State<ProfileInformationScreen> createState() => _ProfileInformationScreenState();
}

class _ProfileInformationScreenState extends State<ProfileInformationScreen> {
  @override
  void initState() {
    Get.put(ProfileRepo());
    final controller = Get.put(ProfileController(profileRepo: Get.find()));

    super.initState();

    WidgetsBinding.instance.addPostFrameCallback((timeStamp) {
      controller.loadProfileInfo();
    });
  }

  @override
  Widget build(BuildContext context) {
    return GetBuilder<ProfileController>(
      builder: (controller) {
        return MyCustomScaffold(
          pageTitle: MyStrings.personalInformation,
          body: SingleChildScrollView(
            clipBehavior: Clip.none,
            child: Skeletonizer(
              enabled: controller.isLoading,
              child: CustomAppCard(
                child: Column(
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        ProfileImageWithUploadButtonWidget(
                          imageUrl: SharedPreferenceService.getUserImage(),
                          imageAlt: SharedPreferenceService.getUserFullName(),
                          showUploadIcon: false,
                        ),
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.end,
                          children: [
                            CustomElevatedBtn(
                              width: Dimensions.space80.w,
                              height: Dimensions.space40.h,
                              text: MyStrings.edit,
                              onTap: () {
                                Get.toNamed(RouteHelper.profileEditScreen);
                              },
                            ),
                            spaceDown(Dimensions.space20),
                            Row(
                              mainAxisAlignment: MainAxisAlignment.start,
                              children: [
                                checkRow(
                                  isCheck: controller.userData?.ev ?? "0",
                                  title: MyStrings.email,
                                ),
                                spaceSide(Dimensions.space5),
                                checkRow(
                                  isCheck: controller.userData?.sv ?? "0",
                                  title: MyStrings.sms,
                                ),
                                spaceSide(Dimensions.space5),
                                checkRow(
                                  isCheck: controller.userData?.kv ?? "0",
                                  title: MyStrings.kyc,
                                ),
                              ],
                            ),
                          ],
                        ),
                      ],
                    ),
                    spaceDown(Dimensions.space25),
                    CustomListTileInformationWidgetCard(
                      width: double.infinity,
                      title: MyStrings.name,
                      subtitle: controller.firstNameController.text,
                    ),
                    spaceDown(Dimensions.space16),
                    CustomListTileInformationWidgetCard(
                      width: double.infinity,
                      title: MyStrings.number,
                      subtitle: "+${SharedPreferenceService.getDialCode()}${controller.mobileNoController.text}",
                    ),
                    spaceDown(Dimensions.space16),
                    CustomListTileInformationWidgetCard(
                      width: double.infinity,
                      title: MyStrings.email,
                      subtitle: controller.emailController.text,
                    ),
                    spaceDown(Dimensions.space16),
                    CustomListTileInformationWidgetCard(
                      width: double.infinity,
                      title: MyStrings.country,
                      subtitle: controller.countryController.text,
                    ),
                  ],
                ),
              ),
            ),
          ),
        );
      },
    );
  }

  Row checkRow({
    required String title,
    required String isCheck,
    TextStyle? subtitleStyle,
  }) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        if (isCheck == "1") ...[
          const Icon(
            Icons.check,
            size: Dimensions.space10,
            color: MyColor.greenLightColor,
          ),
        ] else ...[
          const Icon(
            Icons.close,
            size: Dimensions.space10,
            color: MyColor.redLightColor,
          ),
        ],
        spaceSide(Dimensions.space5),
        Text(
          title.tr,
          style: MyTextStyle.caption1Style..copyWith(color: MyColor.getBodyTextColor()),
        ),
      ],
    );
  }
}
