import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/virtual_card_transactions_list_tile_card.dart';
import 'package:ovopay/app/components/no_data.dart';
import 'package:ovopay/app/components/text/header_text.dart';
import 'package:ovopay/core/utils/util_exporter.dart';

import '../../controller/virtual_cards_controller.dart';

class SingleCardTransactionListCard extends StatelessWidget {
  const SingleCardTransactionListCard({
    super.key,
    required this.historyScrollController,
  });
  final ScrollController historyScrollController;
  @override
  Widget build(BuildContext context) {
    return GetBuilder<VirtualCardsController>(
      builder: (controller) {
        return CustomAppCard(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.center,
            children: [
              Align(
                alignment: AlignmentDirectional.centerStart,
                child: HeaderText(
                  text: MyStrings.transactions.tr,
                  textAlign: TextAlign.start,
                  textStyle: MyTextStyle.sectionTitle2.copyWith(
                    color: MyColor.getHeaderTextColor(),
                  ),
                ),
              ),
              spaceDown(Dimensions.space8),
              Expanded(
                child: (controller.singleTransactions.isEmpty)
                    ? FittedBox(
                        fit: BoxFit.scaleDown,
                        child: NoDataWidget(
                          text: MyStrings.noTransactionsToShow.tr,
                        ),
                      )
                    : ListView.builder(
                        // controller: historyScrollController,
                        itemCount: controller.singleTransactions.length,
                        itemBuilder: (context, index) {
                          var item = controller.singleTransactions[index];
                          bool isLastIndex = index == controller.singleTransactions.length - 1;
                          return VirtualCardTransactionListTileCard(
                            title: "•••• •••• •••• ${item.virtualCard?.last4 ?? "••••"}",
                            subtitle: item.details ?? "",
                            balance: "${item.trxType ?? ""}${MyUtils.getUserAmount(item.amount ?? "")}",
                            date: DateConverter.estimatedDateOrTime(
                              item.createdAt ?? "",
                              customFormat: "dd/MM/yyyy hh:mm aa",
                            ),
                            onPressed: () {},
                            trxType: item.trxType ?? "",
                            showBorder: !isLastIndex,
                          );
                        },
                      ),
              ),
            ],
          ),
        );
      },
    );
  }
}
