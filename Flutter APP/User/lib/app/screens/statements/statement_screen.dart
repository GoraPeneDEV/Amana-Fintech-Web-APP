import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/app/screens/statements/controller/statement_history_controller.dart';
import 'package:ovopay/core/data/repositories/transaction_history/transaction_history_repo.dart';

import 'widgets/statement_balance_history_card.dart';
import 'widgets/statement_balance_swap_card.dart';
import 'package:skeletonizer/skeletonizer.dart';

import '../../../../core/utils/util_exporter.dart';

class StatementScreen extends StatefulWidget {
  const StatementScreen({super.key, this.onItemTapped});
  final Function(int index)? onItemTapped;

  @override
  State<StatementScreen> createState() => _StatementScreenState();
}

class _StatementScreenState extends State<StatementScreen> {
  @override
  void initState() {
    super.initState();

    Get.put(TransactionHistoryRepo());
    final controller = Get.put(
      StatementHistoryController(transactionHistoryRepo: Get.find()),
    );

    // Fetch initial data
    WidgetsBinding.instance.addPostFrameCallback((_) async {
      if (mounted) {
        controller.initialHistoryData();
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return GetBuilder<StatementHistoryController>(
      builder: (controller) {
        return MyCustomScaffold(
          pageTitle: MyStrings.statement,
          onBackButtonTap: (widget.onItemTapped != null)
              ? () {
                  widget.onItemTapped!(0);
                }
              : null,
          body: RefreshIndicator(
            color: MyColor.getPrimaryColor(),
            onRefresh: () async {
              controller.getStatementsHistoryDataList(forceLoad: true);
            },
            child: SingleChildScrollView(
              physics: ClampingScrollPhysics(
                parent: AlwaysScrollableScrollPhysics(),
              ),
              child: Skeletonizer(
                enabled: controller.isStatementLoading,
                child: Column(
                  children: [
                    //Swap balance Card
                    StatementBalanceSwapCard(controller: controller),
                    spaceDown(Dimensions.space16),
                    //History
                    StatementBalanceHistoryCard(controller: controller),
                  ],
                ),
              ),
            ),
          ),
        );
      },
    );
  }
}
