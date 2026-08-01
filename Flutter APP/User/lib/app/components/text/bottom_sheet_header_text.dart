import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/core/utils/my_color.dart';
import 'package:ovopay/core/utils/text_style.dart';

class BottomSheetHeaderText extends StatelessWidget {
  final String text;
  final TextAlign? textAlign;

  const BottomSheetHeaderText({super.key, required this.text, this.textAlign});

  @override
  Widget build(BuildContext context) {
    return Text(
      text.tr,
      textAlign: textAlign,
      overflow: TextOverflow.ellipsis,
      style: MyTextStyle.sectionTitle.copyWith(
        color: MyColor.getHeaderTextColor(),
        fontWeight: FontWeight.w600,
      ),
    );
  }
}
