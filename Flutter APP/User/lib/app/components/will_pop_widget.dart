import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/dialog/exit_dialog.dart';

class WillPopWidget extends StatelessWidget {
  final Widget child;
  final String nextRoute;
  final VoidCallback? action;

  const WillPopWidget({
    super.key,
    required this.child,
    this.nextRoute = '',
    this.action,
  });

  @override
  Widget build(BuildContext context) {
    return PopScope(
      canPop: false,
      onPopInvokedWithResult: (didPop, result) {
        if (didPop) return; // Prevent double pop

        if (nextRoute.isEmpty) {
          if (action == null) {
            showExitDialog(context);
          } else {
            action!();
          }
        } else {
          Get.offAllNamed(nextRoute);
        }
      },
      child: child,
    );
  }
}
