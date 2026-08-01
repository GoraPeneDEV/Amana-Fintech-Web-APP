import 'dart:async';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/core/utils/my_color.dart';
import 'package:ovopay/core/utils/my_strings.dart';
import 'package:permission_handler/permission_handler.dart';
import 'webview_widget.dart';

class MyWebViewScreen extends StatefulWidget {
  final String redirectUrl;
  final String successUrl;
  final String failedUrl;

  const MyWebViewScreen({
    super.key,
    required this.redirectUrl,
    required this.successUrl,
    required this.failedUrl,
  });

  @override
  State<MyWebViewScreen> createState() => _MyWebViewScreenState();
}

class _MyWebViewScreenState extends State<MyWebViewScreen> {
  @override
  void initState() {
    super.initState();
  }

  @override
  void dispose() {
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return MyCustomScaffold(
      pageTitle: MyStrings.payNow.tr,
      body: MyWebViewWidget(
        redirectUrl: widget.redirectUrl,
        successUrl: widget.successUrl,
        failedUrl: widget.failedUrl,
      ),
      // floatingActionButton: cancelButton(),
    );
  }

  Widget cancelButton() {
    return FloatingActionButton(
      backgroundColor: MyColor.redLightColor,
      onPressed: () async {
        Get.back();
      },
      child: Icon(Icons.cancel, color: MyColor.getWhiteColor(), size: 30),
    );
  }

  Future<Map<Permission, PermissionStatus>> permissionServices() async {
    Map<Permission, PermissionStatus> statuses = await [
      Permission.photos,
      Permission.microphone,
      Permission.mediaLibrary,
      Permission.camera,
      Permission.storage,
    ].request();

    return statuses;
  }
}
