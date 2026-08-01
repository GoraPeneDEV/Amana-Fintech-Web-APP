import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/annotated_region/annotated_region_widget.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';

import '../../../core/utils/util_exporter.dart';

class MyCustomScaffold extends StatelessWidget {
  const MyCustomScaffold({
    super.key,
    this.pageTitle = "PageTitle",
    this.actionButton,
    this.body,
    this.hideAppBar = false,
    this.appBarBgColor,
    this.screenBgColor,
    this.padding,
    this.demo = false,
    this.onBackButtonTap,
    this.floatingActionButton,
    this.centerTitle = false,
  });
  final String? pageTitle;
  final List<Widget>? actionButton;
  final Widget? body;
  final bool? hideAppBar;
  final Color? appBarBgColor;
  final Color? screenBgColor;
  final EdgeInsetsGeometry? padding;
  final bool demo;
  final bool centerTitle;
  final VoidCallback? onBackButtonTap;
  final Widget? floatingActionButton;

  @override
  Widget build(BuildContext context) {
    return AnnotatedRegionWidget(
      statusBarColor: appBarBgColor ?? MyColor.white,
      systemNavigationBarColor: appBarBgColor ?? MyColor.white,
      child: Scaffold(
        backgroundColor: screenBgColor ?? MyColor.getScreenBgColor(),
        appBar: hideAppBar == true
            ? null
            : PreferredSize(
                preferredSize: Size.fromHeight(
                  60.0.sp,
                ), // here the desired height(
                child: AppBar(
                  surfaceTintColor: MyColor.transparentColor,
                  centerTitle: centerTitle,
                  backgroundColor: appBarBgColor ?? MyColor.white,
                  leading: IconButton(
                    onPressed: () {
                      if (onBackButtonTap != null) {
                        onBackButtonTap!();
                      } else {
                        Get.back();
                      }
                    },
                    icon: MyAssetImageWidget(
                      color: MyColor.getPrimaryColor(),
                      isSvg: true,
                      assetPath: MyIcons.arrowBackIcon,
                      width: Dimensions.space35.w,
                      height: Dimensions.space35.w,
                    ),
                  ),
                  titleSpacing: 0,
                  title: Text(
                    "$pageTitle".tr,
                    style: MyTextStyle.appBarTitle.copyWith(
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                  actions: [
                    if (actionButton != null) ...[...actionButton!],
                  ],
                ),
              ),
        body: Padding(
          padding: padding ??
              EdgeInsetsDirectional.symmetric(
                horizontal: Dimensions.space16.w,
                vertical: Dimensions.space16.w,
              ),
          child: body,
        ),
        floatingActionButton: floatingActionButton,
      ),
    );
  }
}
