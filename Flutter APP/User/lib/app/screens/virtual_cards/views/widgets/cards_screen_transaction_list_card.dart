import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/virtual_card_transactions_list_tile_card.dart';
import 'package:ovopay/app/components/custom_loader/custom_loader.dart';
import 'package:ovopay/app/components/no_data.dart';
import 'package:ovopay/app/components/shimmer/transaction_history_shimmer.dart';
import 'package:ovopay/app/components/text/header_text.dart';
import 'package:ovopay/core/utils/util_exporter.dart';

import '../../controller/virtual_cards_controller.dart';

class CardScreenTransactionMenuCard extends StatelessWidget {
  const CardScreenTransactionMenuCard({
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
                child: controller.isHistoryLoading
                    ? TransactionHistoryShimmer()
                    : (controller.virtualCardTransactionsList.isEmpty)
                        ? FittedBox(
                            fit: BoxFit.scaleDown,
                            child: NoDataWidget(
                              text: MyStrings.noTransactionsToShow.tr,
                            ),
                          )
                        : ListView.builder(
                            controller: historyScrollController,
                            itemCount: controller.virtualCardTransactionsList.length + 1,
                            itemBuilder: (context, index) {
                              if (controller.virtualCardTransactionsList.length == index) {
                                return controller.hasNext() ? const CustomLoader(isPagination: true) : const SizedBox();
                              }
                              var item = controller.virtualCardTransactionsList[index];
                              bool isLastIndex = index == controller.virtualCardTransactionsList.length - 1;

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
