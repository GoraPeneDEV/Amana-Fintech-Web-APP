import 'package:flutter/material.dart';
import 'package:ovopay/app/components/text/header_text.dart';

import '../../../../../core/utils/util_exporter.dart';

class HeaderWithText extends StatelessWidget {
  final String header;
  final String text;

  const HeaderWithText({super.key, required this.header, required this.text});

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        HeaderText(
          textAlign: TextAlign.start,
          text: header,
          textStyle: MyTextStyle.sectionTitle.copyWith(
            color: MyColor.getBodyTextColor(),
            fontSize: Dimensions.fontLarge,
          ),
        ),
        spaceDown(Dimensions.space3),
        HeaderText(
          textAlign: TextAlign.start,
          text: text,
          textStyle: MyTextStyle.sectionTitle.copyWith(
            fontWeight: FontWeight.normal,
            fontSize: Dimensions.fontSmall,
            color: MyColor.getBodyTextColor(),
          ),
        ),
      ],
    );
  }
}
