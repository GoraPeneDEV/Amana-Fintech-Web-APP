import 'package:flutter/material.dart';
import 'package:ovopay/app/components/bottom-sheet/top_modal_sheet.dart';

import '../../../core/utils/my_color.dart';

class CustomBottomSheetPlus {
  final Widget child;
  final bool isNeedPadding;
  final bool isNeedAnimatedPadding;
  final VoidCallback? voidCallback;
  final Color bgColor;
  final Color? barrierColor;
  final bool enableDrag;
  final bool isDismissible;
  final bool isScrollControlled;
  final bool isFromTop;
  final Offset startOffset;

  CustomBottomSheetPlus({
    required this.child,
    this.isNeedPadding = true,
    this.isNeedAnimatedPadding = true,
    this.isScrollControlled = true,
    this.enableDrag = true,
    this.isDismissible = true,
    this.voidCallback,
    this.bgColor = MyColor.white,
    this.barrierColor,
    this.isFromTop = false,
    this.startOffset = const Offset(0, -1.0),
  });

  void show(BuildContext context) {
    if (isFromTop) {
      showTopModalSheet(
        context,
        child,
        startOffset: startOffset,
        barrierDismissible: isDismissible,
        barrierColor: barrierColor ?? Colors.transparent,
      ).then((value) => voidCallback);
    } else {
      showModalBottomSheet(
        isDismissible: isDismissible,
        barrierColor: barrierColor,
        isScrollControlled: true,
        context: context,
        elevation: 0.0,
        enableDrag: enableDrag,
        backgroundColor: bgColor,
        shape: const RoundedRectangleBorder(
          borderRadius: BorderRadiusDirectional.only(
            topEnd: Radius.circular(25),
            topStart: Radius.circular(25),
          ),
        ),
        builder: (context) {
          return SingleChildScrollView(
            physics: const BouncingScrollPhysics(),
            padding: isNeedPadding == true
                ? const EdgeInsetsDirectional.only(
                    start: 20,
                    end: 20,
                    bottom: 30,
                    top: 8,
                  )
                : EdgeInsets.zero,
            child: AnimatedPadding(
              padding: isNeedAnimatedPadding
                  ? EdgeInsetsDirectional.only(
                      bottom: MediaQuery.of(context).viewInsets.bottom,
                    )
                  : EdgeInsets.zero,
              duration: const Duration(milliseconds: 500),
              curve: Curves.decelerate,
              child: IntrinsicHeight(
                child: ConstrainedBox(
                  constraints: BoxConstraints(
                    maxHeight: MediaQuery.of(context).size.height, // Maximum height
                  ),
                  child: child, // Your child widget goes here
                ),
              ),
            ),
          );
        },
      ).then((value) => voidCallback);
    }
  }
}
