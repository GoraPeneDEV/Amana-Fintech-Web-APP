import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/buttons/app_main_submit_button.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/custom_company_list_tile_card.dart';
import 'package:ovopay/app/components/text/header_text_smaller.dart';
import 'package:ovopay/app/screens/education_fee_screen/controller/education_fee_controller.dart';
import 'package:ovopay/app/screens/global/controller/global_dynamic_form_controller.dart';
import 'package:ovopay/app/screens/global/views/dynamic_form_widget_view.dart';

import '../../../../../core/utils/util_exporter.dart';

class EducationFeeDynamicFormPage extends StatefulWidget {
  const EducationFeeDynamicFormPage({
    super.key,
    required this.context,
    required this.onSuccessCallback,
  });

  final BuildContext context;
  final VoidCallback onSuccessCallback;

  @override
  State<EducationFeeDynamicFormPage> createState() => _EducationFeeDynamicFormPageState();
}

class _EducationFeeDynamicFormPageState extends State<EducationFeeDynamicFormPage> {
  final formKey = GlobalKey<FormState>();
  @override
  Widget build(BuildContext context) {
    return GetBuilder<EducationFeeController>(
      builder: (educationController) {
        return SingleChildScrollView(
          child: Column(
            children: [
              if (educationController.selectedEducationInstitute != null) ...[
                CustomAppCard(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      CustomCompanyListTileCard(
                        padding: EdgeInsets.zero,
                        imagePath: "${educationController.selectedEducationInstitute?.getInstituteImageUrl()}",
                        title: "${educationController.selectedEducationInstitute?.name}",
                        subtitle: "${educationController.educationCategoryDataList.firstWhereOrNull((e) => e.id?.toString() == educationController.selectedEducationInstitute?.categoryId?.toString())?.name}",
                        trailingTitle: MyStrings.customerID.tr,
                        trailingSubtitle: "${educationController.selectedEducationInstitute?.id}",
                        showBorder: false,
                      ),
                    ],
                  ),
                ),
              ],
              spaceDown(Dimensions.space16),
              CustomAppCard(
                width: double.infinity,
                child: Form(
                  key: formKey,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      HeaderTextSmaller(
                        textAlign: TextAlign.center,
                        text: "${MyStrings.paymentInformation.tr} ",
                      ),
                      spaceDown(Dimensions.space24),
                      if (educationController.selectedEducationInstitute != null) ...[
                        DynamicFormWidgetView(
                          formList: educationController.selectedEducationInstitute?.form?.formData?.list ?? [],
                        ),
                      ],
                    ],
                  ),
                ),
              ),
              spaceDown(Dimensions.space15),
              AppMainSubmitButton(
                // isActive: Get.find<GlobalDynamicFormController>().hasError().isEmpty,
                text: MyStrings.continueText,
                onTap: () {
                  if (formKey.currentState?.validate() ?? false) {
                    Get.find<GlobalDynamicFormController>().submitFormDataData(
                      widget.onSuccessCallback,
                    );
                  }
                },
              ),
            ],
          ),
        );
      },
    );
  }
}
