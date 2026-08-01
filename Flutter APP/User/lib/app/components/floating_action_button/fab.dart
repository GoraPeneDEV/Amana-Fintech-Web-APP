import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/core/utils/my_color.dart';
import 'package:get/get_rx/src/rx_typedefs/rx_typedefs.dart';
import 'package:ovopay/core/utils/my_strings.dart';
import 'package:ovopay/core/utils/text_style.dart';

class FAB extends StatelessWidget {
  final Callback callback;
  final Widget? icon;

  const FAB({super.key, required this.callback, this.icon});

  @override
  Widget build(BuildContext context) {
    return FloatingActionButton.extended(
      backgroundColor: MyColor.getWhiteColor(),
      onPressed: callback,
      icon: Padding(padding: const EdgeInsets.all(8.0), child: icon),
      label: Text(
        MyStrings.create.tr,
        style: MyTextStyle.caption1Style.copyWith(
          color: MyColor.getBodyTextColor(),
        ),
      ),
    );
  }
}
