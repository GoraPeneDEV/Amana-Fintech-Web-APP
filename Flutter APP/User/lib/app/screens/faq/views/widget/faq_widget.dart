import 'package:flutter/material.dart';

import 'package:get/get.dart';
import 'package:ovopay/app/components/animated_widget/expanded_widget.dart';
import 'package:ovopay/app/components/card/custom_card.dart';

import '../../../../../core/utils/util_exporter.dart';

class FaqListItem extends StatelessWidget {
  final String question;
  final String answer;
  final int index;
  final int selectedIndex;
  final VoidCallback press;
  final bool showBorder;

  const FaqListItem({
    super.key,
    required this.answer,
    required this.question,
    required this.index,
    required this.press,
    required this.selectedIndex,
    this.showBorder = true,
  });

  @override
  Widget build(BuildContext context) {
    return CustomAppCard(
      onPressed: press,
      margin: EdgeInsetsDirectional.symmetric(vertical: Dimensions.space4.h),
      radius: Dimensions.largeRadius.r,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Expanded(
                child: Text(
                  question.tr,
                  style: MyTextStyle.sectionTitle3.copyWith(
                    color: MyColor.getHeaderTextColor(),
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ),
              SizedBox(
                height: 30,
                width: 30,
                child: Icon(
                  index == selectedIndex ? Icons.keyboard_arrow_up : Icons.keyboard_arrow_down,
                  color: MyColor.getBodyTextColor(),
                  size: 20,
                ),
              ),
            ],
          ),
          ExpandedSection(
            expand: index == selectedIndex,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const SizedBox(height: Dimensions.space10),
                Text(
                  answer.tr,
                  style: MyTextStyle.sectionSubTitle1.copyWith(
                    color: MyColor.getBodyTextColor(),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
