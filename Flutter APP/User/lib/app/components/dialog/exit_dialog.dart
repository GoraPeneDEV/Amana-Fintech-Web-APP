import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:ovopay/app/components/dialog/app_dialog.dart';
import 'package:ovopay/core/utils/my_strings.dart';

void showExitDialog(BuildContext context) {
  AppDialogs.confirmDialogForAll(
    context,
    onConfirmTap: () {
      SystemNavigator.pop();
      // Get.back();
    },
    title: MyStrings.exitTitle,
    subTitle: MyStrings.exitSubTitle,
  );
}
