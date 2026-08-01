import 'package:flutter/material.dart';
import 'package:ovopay/app/components/credit_card_ui/src/ui/credit_card_text.dart' show CreditCardText;

///
class CreditCardHolderNameView extends StatelessWidget {
  ///
  const CreditCardHolderNameView({required this.cardHolderFullName, super.key});

  ///
  final String cardHolderFullName;

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      width: 172,
      child: CreditCardText(
        cardHolderFullName.toUpperCase(),
        letterSpacing: 2,
        fontSize: 12,
      ),
    );
  }
}
