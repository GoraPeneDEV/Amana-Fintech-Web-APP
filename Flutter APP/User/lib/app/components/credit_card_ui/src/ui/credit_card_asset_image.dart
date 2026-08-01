import 'package:flutter/material.dart';

///
class CreditCardAssetImage extends StatelessWidget {
  ///
  const CreditCardAssetImage({required this.assetPath, super.key});

  ///
  final String assetPath;

  @override
  Widget build(BuildContext context) {
    return Image.asset(assetPath, fit: BoxFit.contain);
  }
}
