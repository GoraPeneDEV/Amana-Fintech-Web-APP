import 'package:flutter/material.dart';
import 'package:flutter_spinkit/flutter_spinkit.dart';
import 'package:ovopay/app/components/indicator/indicator.dart';
import 'package:ovopay/core/utils/my_color.dart';

class CustomLoader extends StatelessWidget {
  final bool isFullScreen;
  final bool isPagination;
  final double strokeWidth;
  final Color? loaderColor;

  const CustomLoader({
    super.key,
    this.isFullScreen = false,
    this.isPagination = false,
    this.strokeWidth = 1,
    this.loaderColor,
  });

  @override
  Widget build(BuildContext context) {
    return isFullScreen
        ? SizedBox(
            height: MediaQuery.of(context).size.height,
            width: MediaQuery.of(context).size.width,
            child: Center(
              child: SpinKitThreeBounce(
                color: loaderColor ?? MyColor.getPrimaryColor(),
                size: 20.0,
              ),
            ),
          )
        : isPagination
            ? Center(
                child: Padding(
                  padding: const EdgeInsets.all(10),
                  child: LoadingIndicator(strokeWidth: strokeWidth),
                ),
              )
            : Center(
                child: SpinKitThreeBounce(
                  color: loaderColor ?? MyColor.getPrimaryColor(),
                  size: 20.0,
                ),
              );
  }
}
