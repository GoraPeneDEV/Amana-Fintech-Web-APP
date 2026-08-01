import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/buttons/custom_elevated_button.dart';
import 'package:ovopay/core/utils/util_exporter.dart';

class AppMainSubmitButton extends StatelessWidget {
  final String text;
  final void Function() onTap;
  final bool isActive;
  final bool isLoading;

  const AppMainSubmitButton({
    super.key,
    required this.text,
    required this.onTap,
    this.isActive = true,
    this.isLoading = false,
  });

  @override
  Widget build(BuildContext context) {
    return AnimatedSwitcher(
      duration: const Duration(milliseconds: 300),
      child: CustomElevatedBtn(
        key: ValueKey<bool>(isActive),
        height: Dimensions.space50.h,
        radius: Dimensions.largeRadius.r,
        bgColor: isActive ? MyColor.getPrimaryColor() : MyColor.getScreenBgColor(),
        textColor: isActive ? MyColor.white : MyColor.getPrimaryColor(),
        borderColor: isActive ? MyColor.transparentColor : MyColor.getPrimaryColor(),
        text: text.tr,
        onTap: onTap, // Disable onTap when loading
        isLoading: isLoading,
      ),
    );
  }
}
