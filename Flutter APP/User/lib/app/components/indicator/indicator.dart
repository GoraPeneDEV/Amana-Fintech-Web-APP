import 'package:flutter/material.dart';
import 'package:ovopay/core/utils/my_color.dart';

class LoadingIndicator extends StatelessWidget {
  final double strokeWidth;

  const LoadingIndicator({super.key, this.strokeWidth = 1});

  @override
  Widget build(BuildContext context) {
    return Container(
      height: 40,
      width: 40,
      padding: const EdgeInsets.all(10),
      decoration: BoxDecoration(
        shape: BoxShape.circle,
        color: MyColor.getWhiteColor(),
      ),
      child: CircularProgressIndicator(
        color: MyColor.getPrimaryColor(),
        strokeWidth: 3,
      ),
    );
  }
}
