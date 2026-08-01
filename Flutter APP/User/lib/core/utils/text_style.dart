import 'package:flutter/material.dart';
import 'package:ovopay/core/utils/util_exporter.dart';

class MyTextStyle {
  //Custom
  static TextStyle appBarTitle = TextStyle(
    fontFamily: 'AlbertSans',
    fontSize: 20.sp, // Size in pixels
    fontWeight: FontWeight.w600, // Bold
    height: 25 / 20.sp, // Line height ratio
    letterSpacing: 0.38, // Letter spacing
    color: MyColor.getHeaderTextColor(), // Hex color for --Heading-Text
    fontFamilyFallback: ['sans-serif'],
  );
  static TextStyle appBarActionButtonTextStyleTitle = TextStyle(
    fontFamily: 'AlbertSans',
    fontSize: 15.sp, // Size in pixels
    fontWeight: FontWeight.w600, // Bold
    height: 20 / 15.sp, // Line height ratio
    letterSpacing: -0.5, // Letter spacing
    decorationColor: MyColor.getPrimaryColor(),
    decoration: TextDecoration.underline,
    color: MyColor.getPrimaryColor(), // Hex color for --Heading-Text
    fontFamilyFallback: ['sans-serif'],
  );

  //Big Header
  static TextStyle balanceCardTextStyle = TextStyle(
    fontFamily: 'AlbertSans',
    fontSize: 34.sp, // Size in pixels
    fontWeight: FontWeight.w600, // Bold
    height: 41 / 34.sp, // Line height ratio
    letterSpacing: 0.374, // Letter spacing
    color: MyColor.getWhiteColor(), // Hex color for --Heading-Text
    fontFamilyFallback: ['sans-serif'],
  );
  static TextStyle headerH1 = TextStyle(
    fontFamily: 'AlbertSans',
    fontSize: 28.sp, // Size in pixels
    fontWeight: FontWeight.w600, // Bold
    height: 34 / 28.sp, // Line height ratio
    letterSpacing: 0.36, // Letter spacing
    color: MyColor.getWhiteColor(), // Hex color for --Heading-Text
    fontFamilyFallback: ['sans-serif'],
  );
  static TextStyle headerH3 = TextStyle(
    fontFamily: 'AlbertSans',
    fontSize: 22.sp, // Size in pixels
    fontWeight: FontWeight.w600, // Bold
    height: 28 / 22.sp, // Line height ratio
    letterSpacing: 0.35, // Letter spacing
    color: MyColor.getWhiteColor(), // Hex color for --Heading-Text
    fontFamilyFallback: ['sans-serif'],
  );
  static TextStyle sectionTitle = TextStyle(
    fontFamily: 'AlbertSans',
    fontSize: 17.sp, // Size in pixels
    fontWeight: FontWeight.w600, // Bold
    height: 22 / 17.sp, // Line height ratio
    letterSpacing: -0.408, // Letter spacing
    color: MyColor.getHeaderTextColor(), // Hex color for --Heading-Text
    fontFamilyFallback: ['sans-serif'],
  );
  //Header Small
  static TextStyle sectionTitle2 = TextStyle(
    fontFamily: 'AlbertSans',
    fontSize: 16.sp, // Size in pixels
    fontWeight: FontWeight.w600, // Bold
    height: 21 / 16.sp, // Line height ratio
    letterSpacing: -0.32, // Letter spacing
    color: MyColor.getHeaderTextColor(), // Hex color for --Heading-Text
    fontFamilyFallback: ['sans-serif'],
  );

  //hedaer 3

  static TextStyle sectionTitle3 = TextStyle(
    fontFamily: 'AlbertSans',
    fontSize: 15.sp, // Size in pixels
    fontWeight: FontWeight.w600, // Bold
    height: 20 / 15.sp, // Line height ratio
    letterSpacing: -0.5, // Letter spacing

    color: MyColor.getHeaderTextColor(), // Hex color for --Heading-Text
    fontFamilyFallback: ['sans-serif'],
  );

  static TextStyle sectionSubTitle1 = TextStyle(
    fontFamily: 'AlbertSans',
    fontSize: 13.sp, // Size in pixels
    fontWeight: FontWeight.w400, // Bold
    height: 18 / 13.sp, // Line height ratio
    letterSpacing: -0.078, // Letter spacing
    color: MyColor.getHeaderTextColor(), // Hex color for --Heading-Text
    fontFamilyFallback: ['sans-serif'],
  );

  static TextStyle sectionBodyTextStyle = TextStyle(
    fontFamily: 'AlbertSans',
    fontSize: 15.sp, // Size in pixels
    fontWeight: FontWeight.w400, // Bold
    height: 20 / 15.sp, // Line height ratio
    letterSpacing: -0.24, // Letter spacing
    color: MyColor.getHeaderTextColor(), // Hex color for --Heading-Text
    fontFamilyFallback: ['sans-serif'],
  );

  static TextStyle sectionBodyBoldTextStyle = TextStyle(
    fontFamily: 'AlbertSans',
    fontSize: 15.sp, // Size in pixels
    fontWeight: FontWeight.w600, // Bold
    height: 20 / 15.sp, // Line height ratio
    letterSpacing: -0.5, // Letter spacing
    color: MyColor.getPrimaryColor(), // Hex color for --Heading-Text
    fontFamilyFallback: ['sans-serif'],
  );
  //body
  static TextStyle bodyTextStyle1 = TextStyle(
    fontFamily: 'AlbertSans',
    fontSize: 16.sp, // Size in pixels
    fontWeight: FontWeight.w400, // Bold
    height: 21 / 16.sp, // Line height ratio
    letterSpacing: -0.32, // Letter spacing
    color: MyColor.getWhiteColor(), // Hex color for --Heading-Text
    fontFamilyFallback: ['sans-serif'],
  );

  static TextStyle bodyTextStyle2 = TextStyle(
    fontFamily: 'AlbertSans',
    fontSize: 17.sp, // Size in pixels
    fontWeight: FontWeight.w400, // Bold
    height: 22 / 17.sp, // Line height ratio
    letterSpacing: -0.408, // Letter spacing
    color: MyColor.getHeaderTextColor(), // Hex color for --Heading-Text
    fontFamilyFallback: ['sans-serif'],
  );

  // Caption1 Style
  static TextStyle caption1Style = TextStyle(
    fontFamily: 'AlbertSans', // Ensure the font is added in pubspec.yaml
    fontSize: 12.sp, // Use a responsive size utility
    fontWeight: FontWeight.w400, // Regular weight
    height: 16 / 12, // Line height ratio based on your CSS
    letterSpacing: 0, // Default letter spacing; adjust if needed
    color: MyColor.getHeaderTextColor(), // Custom color utility
    fontFamilyFallback: ['sans-serif'],
  );
  static TextStyle caption2Style = TextStyle(
    fontFamily: 'AlbertSans', // Ensure the font is added in pubspec.yaml
    fontSize: 11.sp, // Use a responsive size utility
    fontWeight: FontWeight.w400, // Regular weight
    height: 13 / 11, // Line height ratio based on your CSS
    letterSpacing: 0.06, // Default letter spacing; adjust if needed
    color: MyColor.getHeaderTextColor(), // Custom color utility
    fontFamilyFallback: ['sans-serif'],
  );

  //Button
  static TextStyle buttonTextStyle1 = TextStyle(
    fontFamily: 'AlbertSans',
    fontSize: 16.sp, // Matches the 16px font size
    fontWeight: FontWeight.w500, // Medium (font-weight: 500)
    height: 19.2 / 16.sp, // Line height ratio (19.2px / 16px = 120%)
    letterSpacing: 0, // No letter spacing specified in CSS
    color: MyColor.getWhiteColor(), // Hex color for --Heading-Text
    fontFamilyFallback: ['sans-serif'],
  );
  //Custom
}
