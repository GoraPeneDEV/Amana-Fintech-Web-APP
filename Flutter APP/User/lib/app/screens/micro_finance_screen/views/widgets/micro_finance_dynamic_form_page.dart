import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/buttons/app_main_submit_button.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/custom_list_tile_card.dart';
import 'package:ovopay/app/components/image/my_network_image_widget.dart';
import 'package:ovopay/app/components/text/header_text_smaller.dart';
import 'package:ovopay/app/screens/global/controller/global_dynamic_form_controller.dart';
import 'package:ovopay/app/screens/global/views/dynamic_form_widget_view.dart';
import 'package:ovopay/app/screens/micro_finance_screen/controller/micro_finance_controller.dart';

import '../../../../../core/utils/util_exporter.dart';

class MicroFinanceDynamicFormPage extends StatefulWidget {
  const MicroFinanceDynamicFormPage({
    super.key,
    required this.context,
    required this.onSuccessCallback,
  });
  final VoidCallback onSuccessCallback;

  final BuildContext context;

  @override
  State<MicroFinanceDynamicFormPage> createState() => _MicroFinanceDynamicFormPageState();
}

class _MicroFinanceDynamicFormPageState extends State<MicroFinanceDynamicFormPage> {
  final formKey = GlobalKey<FormState>();
  @override
  Widget build(BuildContext context) {
    return GetBuilder<MicroFinanceController>(
      builder: (controller) {
        return SingleChildScrollView(
          child: Column(
            children: [
              if (controller.selectedNgo != null) ...[
                CustomAppCard(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      CustomListTileCard(
                        leading: CustomAppCard(
                          width: Dimensions.space45.w,
                          height: Dimensions.space45.w,
                          radius: Dimensions.badgeRadius,
                          padding: EdgeInsetsDirectional.all(
                            Dimensions.space4.w,
                          ),
                          child: MyNetworkImageWidget(
                            boxFit: BoxFit.scaleDown,
                            imageUrl: controller.selectedNgo?.getNgoImageUrl() ?? "",
                            isProfile: false,
                            width: Dimensions.space40.w,
                            height: Dimensions.space40.w,
                          ),
                        ),
                        imagePath: "",
                        title: controller.selectedNgo?.name ?? "",
                        showBorder: false,
                        onPressed: () {},
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
                        text: "${MyStrings.billingDetails.tr} ",
                      ),
                      spaceDown(Dimensions.space24),
                      if (controller.selectedNgo != null) ...[
                        DynamicFormWidgetView(
                          formList: controller.selectedNgo?.form?.formData?.list ?? [],
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
