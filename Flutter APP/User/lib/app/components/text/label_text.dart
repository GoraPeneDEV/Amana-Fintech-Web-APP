import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/core/utils/my_color.dart';
import 'package:ovopay/core/utils/text_style.dart';

class LabelText extends StatelessWidget {
  final bool isRequired;
  final String text;
  final TextAlign? textAlign;
  final TextStyle? textStyle;

  const LabelText({
    super.key,
    required this.text,
    this.textAlign,
    this.textStyle,
    this.isRequired = false,
  });

  @override
  Widget build(BuildContext context) {
    return RichText(
      textAlign: textAlign ?? TextAlign.start,
      text: TextSpan(
        text: text.tr,
        style: textStyle ?? MyTextStyle.bodyTextStyle1.copyWith(color: MyColor.getBlackColor()),
        children: isRequired
            ? [
                TextSpan(
                  text: ' *',
                  style: MyTextStyle.bodyTextStyle1.copyWith(
                    color: MyColor.redLightColor,
                  ),
                ),
              ]
            : [],
      ),
    );
  }
}
