import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/screens/auth/kyc/views/section/kyc_checkbox_section.dart';
import 'package:ovopay/app/screens/auth/kyc/views/section/kyc_date_time_section.dart';
import 'package:ovopay/app/screens/auth/kyc/views/section/kyc_radio_section.dart';
import 'package:ovopay/app/screens/auth/kyc/views/section/kyc_select_section.dart';
import 'package:ovopay/app/screens/auth/kyc/views/section/kyc_text_section.dart';
import 'package:ovopay/app/screens/auth/kyc/views/widget/widget/kyc_dynamic_file_item.dart';
import 'package:ovopay/app/screens/global/controller/global_dynamic_form_controller.dart';
import 'package:ovopay/core/data/models/global/formdata/dynamic_fom_submitted_value_model.dart';
import 'package:ovopay/core/data/models/kyc/kyc_response_model.dart' as kyc;
import 'package:ovopay/core/data/models/kyc/kyc_response_model.dart';

import '../../../../core/utils/util_exporter.dart';

class DynamicFormWidgetView extends StatefulWidget {
  const DynamicFormWidgetView({
    super.key,
    required this.formList,
    this.autoFillDataList,
  });
  final List<KycFormModel> formList;
  final List<UsersDynamicFormSubmittedDataModel>? autoFillDataList;

  @override
  State<DynamicFormWidgetView> createState() => _DynamicFormWidgetViewState();
}

class _DynamicFormWidgetViewState extends State<DynamicFormWidgetView> {
  @override
  void initState() {
    super.initState();
    var controller = Get.put(GlobalDynamicFormController());
    controller.formList.clear();
    controller.beforeInitLoadDynamicFromData(
      widget.formList,
      autoFillDataList: widget.autoFillDataList,
    );
  }

  @override
  Widget build(BuildContext context) {
    return GetBuilder<GlobalDynamicFormController>(
      initState: (state) {
        // printW(state.controller);
        // if (state.controller != null) {
        //   state.controller?.beforeInitLoadDynamicFromData(widget.formList, autoFillDataList: widget.autoFillDataList);
        // }
      },
      builder: (controller) {
        return Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            ListView.builder(
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              scrollDirection: Axis.vertical,
              itemCount: controller.formList.length,
              itemBuilder: (ctx, index) {
                kyc.KycFormModel? model = controller.formList[index];
                bool isLastIndex = index == controller.formList.length - 1; // Use this if childCount is dynamic
                return Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    if (MyUtils.getTextInputType(model.type ?? 'text')) ...[
                      KycTextAnEmailSection(
                        onChanged: (value) {
                          controller.changeSelectedValue(value, index);
                        },
                        model: model,
                      ),
                      if (!isLastIndex) spaceDown(Dimensions.space20),
                    ] else if (model.type == "select") ...[
                      KycSelectSection(
                        onChanged: (value) {
                          controller.changeSelectedValue(value, index);
                        },
                        model: model,
                      ),
                      if (!isLastIndex) spaceDown(Dimensions.space20),
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
                      if (!isLastIndex) spaceDown(Dimensions.space20),
                    ] else if (model.type == "checkbox") ...[
                      KycCheckBoxSection(
                        model: model,
                        onChanged: (value) {
                          controller.changeSelectedCheckBoxValue(index, value);
                        },
                        selectedValue: controller.formList[index].cbSelected,
                      ),
                      if (!isLastIndex) spaceDown(Dimensions.space20),
                    ] else if (model.type == "datetime" || model.type == "date" || model.type == "time") ...[
                      KycDateTimeSection(
                        model: model,
                        onChanged: (value) {
                          controller.changeSelectedValue(value, index);
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
                      if (!isLastIndex) spaceDown(Dimensions.space20),
                    ],
                    if (model.type == "file") ...[
                      KycDynamicFileSelectItem(index: index),
                      if (!isLastIndex) spaceDown(Dimensions.space20),
                    ],
                  ],
                );
              },
            ),
          ],
        );
      },
    );
  }
}
