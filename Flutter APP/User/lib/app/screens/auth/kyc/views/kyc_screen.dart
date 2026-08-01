import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/buttons/custom_elevated_button.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/app/components/no_data.dart';
import 'package:ovopay/app/screens/auth/kyc/controller/kyc_controller.dart';
import 'package:ovopay/app/screens/auth/kyc/views/section/kyc_checkbox_section.dart';
import 'package:ovopay/app/screens/auth/kyc/views/section/kyc_date_time_section.dart';
import 'package:ovopay/app/screens/auth/kyc/views/section/kyc_radio_section.dart';
import 'package:ovopay/app/screens/auth/kyc/views/section/kyc_select_section.dart';
import 'package:ovopay/app/screens/auth/kyc/views/section/kyc_text_section.dart';
import 'package:ovopay/app/screens/auth/kyc/views/widget/already_verifed.dart';
import 'package:ovopay/app/screens/auth/kyc/views/widget/widget/kyc_file_item.dart';
import 'package:ovopay/core/data/models/kyc/kyc_response_model.dart' as kyc;
import 'package:ovopay/core/data/repositories/kyc/kyc_repo.dart';
import 'package:ovopay/core/route/route.dart';
import 'package:skeletonizer/skeletonizer.dart';

import '../../../../../core/utils/util_exporter.dart';

class KycScreen extends StatefulWidget {
  const KycScreen({super.key});

  @override
  State<KycScreen> createState() => _KycScreenState();
}

class _KycScreenState extends State<KycScreen> {
  final formKey = GlobalKey<FormState>();

  @override
  void initState() {
    Get.put(KycRepo());
    Get.put(KycController(repo: Get.find()));
    super.initState();

    WidgetsBinding.instance.addPostFrameCallback((_) {
      Get.find<KycController>().beforeInitLoadKycData();
    });
  }

  @override
  Widget build(BuildContext context) {
    return GetBuilder<KycController>(
      builder: (controller) => GestureDetector(
        onTap: () {
          MyUtils.clearAllTypeFocusNodes();
        },
        child: MyCustomScaffold(
          pageTitle: controller.isAlreadyVerified
              ? MyStrings.kycVerification.tr
              : controller.isAlreadyPending
                  ? MyStrings.kycUnderReviewMsg.tr
                  : MyStrings.kycVerification.tr,
          onBackButtonTap: () {
            Get.offAllNamed(RouteHelper.dashboardScreen);
          },
          actionButton: [
            if (!controller.isAlreadyPending) ...[
              TextButton(
                style: TextButton.styleFrom(
                  overlayColor: MyColor.getPrimaryColor(), // Text color
                  textStyle: TextStyle(
                    fontSize: Dimensions.fontLarge,
                    fontWeight: FontWeight.w600,
                    decoration: TextDecoration.underline,
                    decorationColor: MyColor.getPrimaryColor(),
                  ),
                ),
                onPressed: () {
                  Get.offAllNamed(RouteHelper.dashboardScreen);
                },
                child: Text(
                  MyStrings.skipVerification.tr,
                  style: MyTextStyle.appBarActionButtonTextStyleTitle,
                ),
              ),
            ],
          ],
          body: controller.isLoading
              ? CustomAppCard(
                  child: Skeletonizer(
                    child: ListView.builder(
                      itemCount: 10,
                      itemBuilder: (context, index) {
                        return Padding(
                          padding: const EdgeInsets.only(
                            bottom: Dimensions.space10,
                          ),
                          child: Bone.square(
                            size: Dimensions.space60,
                            borderRadius: BorderRadius.circular(
                              Dimensions.largeRadius.r,
                            ),
                          ),
                        );
                      },
                    ),
                  ),
                )
              : controller.isAlreadyVerified
                  ? const AlreadyVerifiedWidget()
                  : controller.isAlreadyPending
                      ? const AlreadyVerifiedWidget(isPending: true)
                      : controller.isNoDataFound
                          ? const NoDataWidget()
                          : SingleChildScrollView(
                              child: Column(
                                children: [
                                  CustomAppCard(
                                    child: Form(
                                      key: formKey,
                                      child: Column(
                                        crossAxisAlignment: CrossAxisAlignment.start,
                                        children: [
                                          ListView.builder(
                                            shrinkWrap: true,
                                            physics: const NeverScrollableScrollPhysics(),
                                            scrollDirection: Axis.vertical,
                                            itemCount: controller.formList.length,
                                            itemBuilder: (ctx, index) {
                                              kyc.KycFormModel? model = controller.formList[index];
                                              return Column(
                                                crossAxisAlignment: CrossAxisAlignment.start,
                                                mainAxisSize: MainAxisSize.min,
                                                children: [
                                                  if (MyUtils.getTextInputType(
                                                    model.type ?? 'text',
                                                  )) ...[
                                                    KycTextAnEmailSection(
                                                      onChanged: (value) {
                                                        controller.changeSelectedValue(
                                                          value,
                                                          index,
                                                        );
                                                      },
                                                      model: model,
                                                    ),
                                                    spaceDown(Dimensions.space20),
                                                  ] else if (model.type == "select") ...[
                                                    KycSelectSection(
                                                      onChanged: (value) {
                                                        controller.changeSelectedValue(
                                                          value,
                                                          index,
                                                        );
                                                      },
                                                      model: model,
                                                    ),
                                                    spaceDown(Dimensions.space20),
                                                  ] else if (model.type == 'radio') ...[
                                                    KycRadioSection(
                                                      model: model,
                                                      onChanged: (selectedIndex) {
                                                        controller.changeSelectedRadioBtnValue(
                                                          index,
                                                          selectedIndex,
                                                        );
                                                      },
                                                      selectedIndex: controller.formList[index].options?.indexOf(
                                                            model.selectedValue ?? '',
                                                          ) ??
                                                          0,
                                                    ),
                                                    spaceDown(Dimensions.space20),
                                                  ] else if (model.type == "checkbox") ...[
                                                    KycCheckBoxSection(
                                                      model: model,
                                                      onChanged: (value) {
                                                        controller.changeSelectedCheckBoxValue(
                                                          index,
                                                          value,
                                                        );
                                                      },
                                                      selectedValue: controller.formList[index].cbSelected,
                                                    ),
                                                    spaceDown(Dimensions.space20),
                                                  ] else if (model.type == "datetime" || model.type == "date" || model.type == "time") ...[
                                                    KycDateTimeSection(
                                                      model: model,
                                                      onChanged: (value) {
                                                        controller.changeSelectedValue(
                                                          value,
                                                          index,
                                                        );
                                                      },
                                                      onTap: () {
                                                        if (model.type == "time") {
                                                          controller.changeSelectedTimeOnlyValue(
                                                            index,
                                                            context,
                                                          );
                                                        } else if (model.type == "date") {
                                                          controller.changeSelectedDateOnlyValue(
                                                            index,
                                                            context,
                                                          );
                                                        } else {
                                                          controller.changeSelectedDateTimeValue(
                                                            index,
                                                            context,
                                                          );
                                                        }
                                                      },
                                                      controller: controller.formList[index].textEditingController!,
                                                    ),
                                                    spaceDown(Dimensions.space20),
                                                  ],
                                                  if (model.type == "file") ...[
                                                    KycFileSelectItem(index: index),
                                                    spaceDown(Dimensions.space20),
                                                  ],
                                                ],
                                              );
                                            },
                                          ),
                                        ],
                                      ),
                                    ),
                                  ),
                                  spaceDown(Dimensions.space15),
                                  CustomElevatedBtn(
                                    radius: Dimensions.largeRadius.r,
                                    bgColor: MyColor.getPrimaryColor(),
                                    isLoading: controller.submitLoading,
                                    text: MyStrings.continueText,
                                    onTap: () {
                                      MyUtils.clearAllTypeFocusNodes();
                                      if (formKey.currentState?.validate() ?? false) {
                                        printW("validated");
                                        controller.submitKycData();
                                      } else {
                                        // Form is invalid, show validation errors
                                      }
                                    },
                                  ),
                                ],
                              ),
                            ),
        ),
      ),
    );
  }
}
