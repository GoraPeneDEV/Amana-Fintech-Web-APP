import 'package:flutter/widgets.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/column_widget/card_column.dart';

import '../../../core/utils/util_exporter.dart';

class AmountDetailsCard extends StatelessWidget {
  final String? amount, charge, total, quantity;
  final String? firstTitle;
  final String? centerTitle;
  final String? endTitle;

  const AmountDetailsCard({super.key, this.amount, this.charge, this.total, this.firstTitle, this.endTitle, this.centerTitle, this.quantity});

  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        if (amount != null) ...[
          Expanded(
            flex: 4,
            child: CardColumn(
              headerTextStyle: MyTextStyle.caption1Style.copyWith(
                color: MyColor.getBodyTextColor(),
              ),
              header: firstTitle ?? MyStrings.amount.tr,
              body: amount ?? "",
              space: 5,
              crossAxisAlignment: CrossAxisAlignment.start,
              isBodyEllipsis: false,
            ),
          ),
          Container(
            height: Dimensions.space50,
            width: 1,
            color: MyColor.getBorderColor(),
            margin: const EdgeInsets.symmetric(horizontal: 10),
          ),
        ],
        if (quantity != null) ...[
          Expanded(
            flex: 4,
            child: CardColumn(
              headerTextStyle: MyTextStyle.caption1Style.copyWith(
                color: MyColor.getBodyTextColor(),
              ),
              header: MyStrings.qty.tr,
              body: quantity ?? "",
              space: 3,
              crossAxisAlignment: CrossAxisAlignment.center,
              // bodyTextStyle: boldMediumLarge.copyWith(fontSize: 16),
            ),
          ),
          Container(
            height: Dimensions.space50,
            width: 1,
            color: MyColor.getBorderColor(),
            margin: const EdgeInsets.symmetric(horizontal: 5),
          ),
        ],
        if (charge != null) ...[
          Expanded(
            flex: 4,
            child: CardColumn(
              headerTextStyle: MyTextStyle.caption1Style.copyWith(
                color: MyColor.getBodyTextColor(),
              ),
              header: centerTitle ?? MyStrings.charge.tr,
              body: charge ?? "",
              space: 5,
              crossAxisAlignment: CrossAxisAlignment.center,
              // bodyTextStyle: boldMediumLarge.copyWith(fontSize: 16),
            ),
          ),
          Container(
            height: Dimensions.space50,
            width: 1,
            color: MyColor.getBorderColor(),
            margin: const EdgeInsets.symmetric(horizontal: 5),
          ),
        ],
        if (total != null) ...[
          Expanded(
            flex: 4,
            child: CardColumn(
              headerTextStyle: MyTextStyle.caption1Style.copyWith(
                color: MyColor.getBodyTextColor(),
              ),
              header: endTitle ?? MyStrings.total.tr,
              body: total ?? "",
              isBodyEllipsis: false,
              space: 5,
              crossAxisAlignment: CrossAxisAlignment.end,
            ),
          ),
        ],
      ],
    );
  }
}
