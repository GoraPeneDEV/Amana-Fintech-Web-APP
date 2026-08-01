import 'package:flutter/material.dart';
import 'package:pin_code_fields/pin_code_fields.dart';
import 'package:ovopay/core/utils/text_style.dart';
import 'package:ovopay/core/utils/dimensions.dart';
import 'package:ovopay/core/utils/my_color.dart';

class OTPFieldWidget extends StatelessWidget {
  const OTPFieldWidget({
    super.key,
    required this.onChanged,
    this.edgeInsets,
    this.controller,
    this.appContext,
  });

  final ValueChanged<String>? onChanged;
  final BuildContext? appContext;
  final EdgeInsets? edgeInsets;
  final TextEditingController? controller;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: edgeInsets ?? EdgeInsets.zero, // Add horizontal padding
      child: Center(
        child: LayoutBuilder(
          builder: (context, constraints) {
            // Calculate the total width required for all fields
            double fieldWidth = 45; // Field width of each PinCodeTextField field
            double separatorWidth = Dimensions.space2; // Width between each field
            int numberOfFields = 6; // Adjust the number of fields as needed

            // Total width needed for fields and separators
            double totalFieldWidth = numberOfFields * fieldWidth + (numberOfFields - 1) * separatorWidth;

            // Check if scrolling is needed based on available space
            bool isScrollable = totalFieldWidth > constraints.maxWidth;

            return SingleChildScrollView(
              clipBehavior: Clip.none,
              scrollDirection: isScrollable ? Axis.horizontal : Axis.vertical,
              child: SizedBox(
                width: isScrollable
                    ? totalFieldWidth
                    : MediaQuery.of(context).size.width > 580
                        ? MediaQuery.of(context).size.width / 1.8
                        : constraints.maxWidth, // Constrain width to avoid layout overflow
                height: fieldWidth * 1.1, // Define a fixed height for the PinCodeTextField
                child: PinCodeTextField(
                  controller: controller,
                  appContext: appContext ?? context,
                  length: numberOfFields, // Number of fields
                  obscureText: false,
                  obscuringCharacter: '•',
                  blinkWhenObscuring: false,
                  animationType: AnimationType.fade,
                  pastedTextStyle: MyTextStyle.sectionBodyTextStyle.copyWith(
                    color: MyColor.getBodyTextColor(),
                  ),
                  textStyle: MyTextStyle.sectionBodyBoldTextStyle.copyWith(
                    color: MyColor.getBodyTextColor(),
                  ),
                  pinTheme: PinTheme(
                    shape: PinCodeFieldShape.box,
                    borderWidth: 0.87,
                    borderRadius: BorderRadius.circular(Dimensions.largeRadius),
                    fieldHeight: fieldWidth,
                    fieldWidth: fieldWidth,
                    inactiveColor: MyColor.getBorderColor(),
                    inactiveFillColor: MyColor.transparentColor,
                    activeFillColor: MyColor.transparentColor,
                    activeColor: MyColor.getBorderColor(),
                    selectedFillColor: MyColor.transparentColor,
                    selectedColor: MyColor.getHeaderTextColor(),
                  ),
                  separatorBuilder: (context, index) => SizedBox(width: separatorWidth),
                  cursorColor: MyColor.getHeaderTextColor(),
                  animationDuration: const Duration(milliseconds: 100),
                  enableActiveFill: true,
                  keyboardType: TextInputType.number,
                  beforeTextPaste: (text) {
                    return true;
                  },
                  onChanged: (value) => onChanged!(value),
                ),
              ),
            );
          },
        ),
      ),
    );
  }
}
