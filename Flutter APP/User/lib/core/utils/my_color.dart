import 'package:flutter/material.dart';
import 'package:ovopay/core/data/services/service_exporter.dart';
import 'package:ovopay/core/utils/util_exporter.dart';
import 'package:ovopay/environment.dart';

class MyColor {
  //BG
  static const Color screenBGColor = Color(0xFFF8FAFC);

  // Primary and Secondary
  static const Color primary = Color(0xFF011B67);
  static const Color secondary = Color(0xFFEEBE2B);

  // Text Colors
  static const Color headingText = Color(0xFF1E293B);
  static const Color bodyText = Color(0xFF475569);

  // Dark and Light
  static const Color dark = Color(0xFF1F2937);
  static const Color light = Color(0xFFF9FAFB);

  // Black and White
  static const Color black = Color(0xFF000000);
  static const Color white = Color(0xFFFFFFFF);

  // Accent
  static const Color accent1 = Color(0xFF77AAFF);

  // Section Background and Border Color
  static const Color borderColor1 = Color(0xFFE2E8F0);
  static const Color borderColor2 = Color(0xFFF8FAFC);
  // Information, Warning, Success, and Error/Danger
  static const Color information = Color(0xFF18B800);
  static const Color warning = Color(0xFFFFCC00);
  static const Color success = Color(0xFF35C75A);
  static const Color error = Color(0xFFEB4E3D);
  static const Color statusColor = Color(0xFF011B67);
  static const Color indigoColor = Color(0xFF6366F1);
  static const Color goldenColor = Color(0xFFFBBF24);
  static const Color greenLightColor = Color(0xFF10B981);
  static const Color violateColor = Color(0xFFA855F7);
  static const Color redLightColor = Color(0xFFEF4444);
  static const Color skyBlueColor = Color(0xFF3B82F6);
  static const Color orangeColor = Color(0xFFF97316);
  static const Color beigeColor = Color(0xFFfff8ea);
  static const Color lemonadeColor = Color(0xFFe5f9ed);

  //Designs

  //Others
  static const Color transparentColor = Colors.transparent;

  //METHODS
  static Color getPrimaryColor() {
    try {
      return Environment.IS_COLOR_FROM_INTERNET
          ? MyUtils.colorFromHex(
              SharedPreferenceService.getGeneralSettingData().data?.generalSetting?.baseColor ?? "",
              defColor: primary,
            )
          : primary;
    } catch (e) {
      return primary;
    }
  }

  static Color getTransparentColor() {
    return transparentColor;
  }

  static Color getBorderColor() {
    return borderColor1;
  }

  static Color getBorderColor2() {
    return borderColor2;
  }

  static Color getTextFieldDisableBorder() {
    return borderColor2;
  }

  static Color getBlackColor() {
    return black;
  }

  static Color getDarkColor() {
    return dark;
  }

  static Color getWhiteColor() {
    return white;
  }

  static Color getHeaderTextColor() {
    return headingText;
  }

  static Color getBodyTextColor() {
    return bodyText;
  }

  static Color getScreenBgColor() {
    return screenBGColor;
  }

  static Color getScreenBgColorWhite() {
    return white;
  }
}
