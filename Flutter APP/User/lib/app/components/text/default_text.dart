import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/core/utils/text_style.dart';

class DefaultText extends StatelessWidget {
  final String text;
  final TextAlign? textAlign;
  final TextStyle? textStyle;

  const DefaultText({
    super.key,
    required this.text,
    this.textAlign,
    this.textStyle,
  });

  @override
  Widget build(BuildContext context) {
    return Text(
      text.tr,
      textAlign: textAlign,
      style: textStyle ?? MyTextStyle.sectionTitle3,
    );
  }
}
