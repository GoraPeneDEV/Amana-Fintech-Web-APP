import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/bottom-sheet/bottom_sheet_header_row.dart';
import 'package:ovopay/app/components/bottom-sheet/custom_bottom_sheet_plus.dart';
import 'package:ovopay/app/components/buttons/custom_elevated_button.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/screens/transaction_history/views/widgets/custom_main_transactions_list_tile_card.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/app/components/custom_loader/custom_loader.dart';
import 'package:ovopay/app/components/drop_down/my_drop_down_widget.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/app/components/no_data.dart';
import 'package:ovopay/app/components/shimmer/transaction_history_shimmer.dart';
import 'package:ovopay/app/components/text-field/rounded_text_field.dart';
import 'package:ovopay/app/screens/transaction_history/controller/transaction_history_controller.dart';
import 'package:ovopay/core/data/models/transaction_history/transaction_history_model.dart';
import 'package:ovopay/core/data/repositories/transaction_history/transaction_history_repo.dart';

import '../../../../../../core/utils/util_exporter.dart';

class TransactionHistoryScreen extends StatefulWidget {
  const TransactionHistoryScreen({super.key, this.onItemTapped});
  final Function(int index)? onItemTapped;
  @override
  State<TransactionHistoryScreen> createState() => _TransactionHistoryScreenState();
}

class _TransactionHistoryScreenState extends State<TransactionHistoryScreen> {
  final ScrollController historyScrollController = ScrollController();
  void fetchData() {
    Get.find<TransactionHistoryController>().getTransactionHistoryDataList(
      forceLoad: false,
    );
  }

  void scrollListener() {
    if (historyScrollController.position.pixels == historyScrollController.position.maxScrollExtent) {
      if (Get.find<TransactionHistoryController>().hasNext()) {
        fetchData();
      }
    }
  }

  @override
  void initState() {
    super.initState();

    Get.put(TransactionHistoryRepo());
    final controller = Get.put(
      TransactionHistoryController(transactionHistoryRepo: Get.find()),
    );

    // Fetch initial data
    WidgetsBinding.instance.addPostFrameCallback((_) async {
      if (mounted) {
        controller.initialHistoryData(); // Receiver if index is 0, Sender otherwise

        // Add scroll listeners
        historyScrollController.addListener(() => scrollListener());
      }
    });
  }

  @override
  void dispose() {
    historyScrollController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return GetBuilder<TransactionHistoryController>(
      builder: (controller) {
        return MyCustomScaffold(
          pageTitle: MyStrings.transactionHistory,
          onBackButtonTap: (widget.onItemTapped != null)
              ? () {
                  widget.onItemTapped!(0);
                }
              : null,
          actionButton: [
            CustomAppCard(
              onPressed: () {
                CustomBottomSheetPlus(
                  child: SafeArea(child: buildFilterSection()),
                ).show(context);
              },
              width: Dimensions.space40.w,
              height: Dimensions.space40.w,
              padding: EdgeInsetsDirectional.all(Dimensions.space8.w),
              radius: Dimensions.largeRadius.r,
              borderColor: MyColor.getPrimaryColor(),
              child: MyAssetImageWidget(
                isSvg: true,
                assetPath: MyIcons.filterIcon,
                width: Dimensions.space24.w,
                height: Dimensions.space24.w,
                color: MyColor.getPrimaryColor(),
              ),
            ),
            spaceSide(Dimensions.space16.w),
          ],
          body: CustomAppCard(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Flexible(
                  fit: FlexFit.loose,
                  child: controller.isHistoryLoading
                      ? TransactionHistoryShimmer()
                      : (controller.transactionHistoryList.isEmpty)
                          ? SingleChildScrollView(
                              child: NoDataWidget(
                                text: MyStrings.noTransactionsToShow.tr,
                              ),
                            )
                          : RefreshIndicator(
                              color: MyColor.getPrimaryColor(),
                              onRefresh: () async {
                                controller.initialHistoryData();
                              },
                              child: ListView.builder(
                                physics: ClampingScrollPhysics(
                                  parent: AlwaysScrollableScrollPhysics(),
                                ),
                                controller: historyScrollController,
                                itemCount: controller.transactionHistoryList.length + 1,
                                itemBuilder: (context, index) {
                                  if (controller.transactionHistoryList.length == index) {
                                    return controller.hasNext() ? const CustomLoader(isPagination: true) : const SizedBox();
                                  }
                                  var item = controller.transactionHistoryList[index];
                                  bool isLastIndex = index == controller.transactionHistoryList.length - 1;

                                  return CustomMainTransactionListTileCard(
                                    item: item,
                                    title: controller.getRemarkText(
                                      "${item.remark}",
                                    ),
                                    showBorder: !isLastIndex,
                                    balance: MyUtils.getUserAmount(
                                      item.amount ?? "",
                                    ),
                                    trxType: "${item.trxType}",
                                    date: DateConverter.estimatedDateOrTime(
                                      item.createdAt ?? "",
                                      customFormat: "dd/MM/yyyy hh:mm aa",
                                    ),
                                    onPressed: () {},
                                    remark: "${item.remark}",
                                  );
                                },
                              ),
                            ),
                ),
              ],
            ),
          ),
        );
      },
    );
  }

  Widget buildFilterSection() {
    return GetBuilder<TransactionHistoryController>(
      builder: (transactionHistoryController) {
        return StatefulBuilder(
          builder: (context, setEr) {
            return Column(
              children: [
                BottomSheetHeaderRow(header: MyStrings.filter),
                spaceDown(Dimensions.space20),
                AppDropdownWidget<TransactionsRemark>(
                  items: transactionHistoryController.transactionHistoryRemarkList,
                  itemToString: (value) {
                    return transactionHistoryController.getRemarkText(
                      value.remark ?? "",
                    );
                  },
                  onItemSelected: (v) {
                    transactionHistoryController.onSelectRemarkChanges(v);
                  },
                  selectedItem: transactionHistoryController.selectedRemark,
                  child: RoundedTextField(
                    readOnly: true,
                    labelText: MyStrings.remark.tr,
                    hintText: '',
                    controller: TextEditingController(
                      text: transactionHistoryController.getRemarkText(
                        transactionHistoryController.selectedRemark?.remark,
                      ),
                    ),
                    textInputAction: TextInputAction.next,
                    keyboardType: TextInputType.phone,
                    onTap: () {},
                    suffixIcon: Icon(
                      Icons.keyboard_arrow_down_rounded,
                      color: MyColor.getDarkColor(),
                    ),
                  ),
                ),
                spaceDown(Dimensions.space20),
                AppDropdownWidget<String>(
                  items: transactionHistoryController.dropdownItemsOrderBy,
                  onItemSelected: (v) {
                    transactionHistoryController.onSelectOrderByChanges(v);
                  },
                  selectedItem: transactionHistoryController.selectOrderBy,
                  child: RoundedTextField(
                    readOnly: true,
                    labelText: MyStrings.orderBy.tr,
                    hintText: '',
                    controller: TextEditingController(
                      text: "${transactionHistoryController.selectOrderBy}",
                    ),
                    textInputAction: TextInputAction.next,
                    keyboardType: TextInputType.phone,
                    onTap: () {},
                    suffixIcon: Icon(
                      Icons.keyboard_arrow_down_rounded,
                      color: MyColor.getDarkColor(),
                    ),
                  ),
                ),
                spaceDown(Dimensions.space20),
                AppDropdownWidget(
                  items: transactionHistoryController.dropdownItemsTrxType,
                  onItemSelected: (v) {
                    transactionHistoryController.onSelectTrxTypeChanges(v);
                  },
                  selectedItem: transactionHistoryController.selectTrxType,
                  child: RoundedTextField(
                    readOnly: true,
                    labelText: MyStrings.trxType.tr,
                    hintText: '',
                    controller: TextEditingController(
                      text: "${transactionHistoryController.selectTrxType}",
                    ),
                    textInputAction: TextInputAction.next,
                    keyboardType: TextInputType.phone,
                    onTap: () {},
                    suffixIcon: Icon(
                      Icons.keyboard_arrow_down_rounded,
                      color: MyColor.getDarkColor(),
                    ),
                  ),
                ),
                spaceDown(Dimensions.space20),
                RoundedTextField(
                  labelText: MyStrings.trxId.tr,
                  hintText: MyStrings.searchByTrxId.tr,
                  textInputAction: TextInputAction.done,
                  keyboardType: TextInputType.text,
                  controller: transactionHistoryController.searchTrxNoController,
                ),
                spaceDown(Dimensions.space20),
                Row(
                  children: [
                    Expanded(
                      child: CustomElevatedBtn(
                        radius: Dimensions.largeRadius.r,
                        textColor: MyColor.getDarkColor(),
                        bgColor: MyColor.getWhiteColor(),
                        borderColor: MyColor.getBorderColor(),
                        text: MyStrings.reset,
                        onTap: () {
                          transactionHistoryController.resetFilter();
                          Get.back();
                        },
                      ),
                    ),
                    spaceSide(Dimensions.space15),
                    Expanded(
                      child: CustomElevatedBtn(
                        isLoading: transactionHistoryController.isHistoryLoading,
                        radius: Dimensions.largeRadius.r,
                        bgColor: MyColor.getPrimaryColor(),
                        text: MyStrings.filter,
                        onTap: () async {
                          await transactionHistoryController.initialHistoryData().whenComplete(() {
                            Get.back();
                          });
                        },
                      ),
                    ),
                  ],
                ),
              ],
            );
          },
        );
      },
    );
  }
}
