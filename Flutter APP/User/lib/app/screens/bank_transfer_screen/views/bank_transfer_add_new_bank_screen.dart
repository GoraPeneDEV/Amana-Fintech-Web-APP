import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/buttons/app_main_submit_button.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/app/components/text-field/rounded_text_field.dart';
import 'package:ovopay/app/components/text/header_text_smaller.dart';
import 'package:ovopay/app/screens/bank_transfer_screen/controller/bank_transfer_controller.dart';
import 'package:ovopay/app/screens/global/controller/global_dynamic_form_controller.dart';

import '../../../../../core/utils/util_exporter.dart';

class BankTransferAddNewBankAccountScreen extends StatefulWidget {
  const BankTransferAddNewBankAccountScreen({super.key});

  @override
  State<BankTransferAddNewBankAccountScreen> createState() => _BankTransferAddNewBankAccountScreenState();
}

class _BankTransferAddNewBankAccountScreenState extends State<BankTransferAddNewBankAccountScreen> {
  final formKey = GlobalKey<FormState>();
  @override
  Widget build(BuildContext context) {
    return MyCustomScaffold(
      pageTitle: MyStrings.addBankAccount,
      body: GetBuilder<BankTransferController>(
        builder: (controller) {
          return PopScope(
            onPopInvokedWithResult: (didPop, result) {
              if (didPop) {
                controller.clearFormData(
                  moreCallback: () {
                    try {
                      Get.find<GlobalDynamicFormController>().formList.clear();
                      Get.find<GlobalDynamicFormController>().update();
                    } catch (e) {
                      printE(e);
                    }
                  },
                );
              }
            },
            child: SingleChildScrollView(
              child: Column(
                children: [
                  CustomAppCard(
                    width: double.infinity,
                    child: Form(
                      key: formKey,
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          HeaderTextSmaller(
                            textAlign: TextAlign.center,
                            text: "${MyStrings.accountInformation.tr} ",
                          ),
                          spaceDown(Dimensions.space24),
                          RoundedTextField(
                            isRequired: true,
                            controller: controller.bankAccountNameController,
                            showLabelText: true,
                            labelText: MyStrings.accountName,
                            hintText: "",
                            textInputAction: TextInputAction.next,
                            keyboardType: TextInputType.text,
                            validator: (value) {
                              if (value.toString().isEmpty) {
                                return MyStrings.kAccountNameNullError.tr;
                              } else {
                                return null;
                              }
                            },
                          ),
                          spaceDown(Dimensions.space16),
                          RoundedTextField(
                            isRequired: true,
                            controller: controller.bankAccountNumberController,
                            showLabelText: true,
                            labelText: MyStrings.accountNumber,
                            hintText: "",
                            textInputAction: TextInputAction.next,
                            keyboardType: TextInputType.number,
                            textInputFormatter: [
                              FilteringTextInputFormatter.allow(
                                RegExp(r'[0-9.]'),
                              ), // Allows digits and a decimal point
                              FilteringTextInputFormatter.deny(
                                RegExp(r'(\.\d{30,})'),
                              ), // Limits decimal places (optional, adjust as needed)
                            ],
                            validator: (value) {
                              if (value.toString().isEmpty) {
                                return MyStrings.kAccountNumberNullError.tr;
                              } else {
                                return null;
                              }
                            },
                          ),
                        ],
                      ),
                    ),
                  ),
                  spaceDown(Dimensions.space15),
                  AppMainSubmitButton(
                    isLoading: controller.isSubmitSaveBankLoading,
                    text: MyStrings.save,
                    onTap: () {
                      if (formKey.currentState?.validate() ?? false) {
                        controller.submitSaveBankAccountProcess(
                          dynamicFormList: [],
                          onSuccessCallback: () {
                            Get.back(result: "success");
                          },
                        );
                      }
                    },
                  ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }
}
