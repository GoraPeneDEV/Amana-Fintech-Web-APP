import 'package:flutter/material.dart';
import 'package:get/utils.dart';
import 'package:ovopay/app/components/bottom-sheet/bottom_sheet_header_row.dart';
import 'package:ovopay/app/components/card/custom_company_list_tile_card.dart';
import 'package:ovopay/app/components/column_widget/card_column.dart';
import 'package:ovopay/app/components/image/my_network_image_widget.dart';
import 'package:ovopay/core/data/models/transaction_history/transaction_history_model.dart';

import '../../../../../core/utils/util_exporter.dart';

class TransactionHistoryBottomSheetDetailsCard extends StatelessWidget {
  const TransactionHistoryBottomSheetDetailsCard({
    super.key,
    required this.item,
    required this.context,
    required this.remarkTitle,
  });

  final TransactionHistoryModel item;
  final String remarkTitle;
  final BuildContext context;

  @override
  Widget build(BuildContext context) {
    return SingleChildScrollView(
      child: Column(
        children: [
          BottomSheetHeaderRow(header: remarkTitle),
          spaceDown(Dimensions.space10),
          buildTransactionHeaderInfo(),
          spaceDown(Dimensions.space10),
          Container(
            height: 1,
            width: double.infinity,
            color: MyColor.getBorderColor(),
            margin: const EdgeInsets.symmetric(horizontal: 10),
          ),
          spaceDown(Dimensions.space10),

          //Number and time
          Row(
            children: [
              if ([
                "send_money",
                "cash_in",
                "cash_out",
                "make_payment",
                "request_money_accept",
                "requested_money_fund_added",
                "receive_money",
                "education_fee",
                "reject_education_fee",
                "bank_transfer",
                "reject_bank_transfer",
                "utility_bill",
                "reject_utility_bill",
                "mobile_recharge",
                "reject_mobile_recharge",
                "microfinance",
                "reject_microfinance",
                "donation",
              ].contains(item.remark)) ...[
                buildTransactionHeaderInfo(type: "account_info"),
              ] else ...[
                Expanded(
                  child: CardColumn(
                    headerTextStyle: MyTextStyle.caption1Style.copyWith(
                      color: MyColor.getBodyTextColor(),
                    ),
                    bodyTextStyle: MyTextStyle.sectionTitle3.copyWith(
                      fontSize: Dimensions.fontLarge,
                      color: MyColor.getHeaderTextColor(),
                    ),
                    header: MyStrings.details.tr,
                    subBody: item.details ?? "",
                    bodyMaxLine: 5,
                    space: 5,
                    crossAxisAlignment: CrossAxisAlignment.start,
                  ),
                ),
              ],
              Container(
                height: Dimensions.space50,
                width: 1,
                color: MyColor.getBorderColor(),
                margin: const EdgeInsets.symmetric(horizontal: 10),
              ),
              Expanded(
                child: CardColumn(
                  headerTextStyle: MyTextStyle.caption1Style.copyWith(
                    color: MyColor.getBodyTextColor(),
                  ),
                  bodyTextStyle: MyTextStyle.sectionTitle3.copyWith(
                    fontSize: Dimensions.fontLarge,
                    color: MyColor.getHeaderTextColor(),
                  ),
                  header: MyStrings.time,
                  body: DateConverter.estimatedDateOrTime(
                    item.createdAt ?? "0",
                  ),
                  isBodyEllipsis: false,
                  space: 5,
                  crossAxisAlignment: CrossAxisAlignment.end,
                ),
              ),
            ],
          ),
          spaceDown(Dimensions.space10),
          Container(
            height: 1,
            width: double.infinity,
            color: MyColor.getBorderColor(),
            margin: const EdgeInsets.symmetric(horizontal: 10),
          ),
          spaceDown(Dimensions.space10),
          //amount and charge
          Row(
            children: [
              Expanded(
                child: CardColumn(
                  headerTextStyle: MyTextStyle.caption1Style.copyWith(
                    color: MyColor.getBodyTextColor(),
                  ),
                  bodyTextStyle: MyTextStyle.sectionTitle3.copyWith(
                    fontSize: Dimensions.fontLarge,
                    color: MyColor.getHeaderTextColor(),
                  ),
                  header: MyStrings.amount.tr,
                  body: MyUtils.getUserAmount(item.amount ?? ""),
                  space: 5,
                  crossAxisAlignment: CrossAxisAlignment.start,
                ),
              ),
              Container(
                height: Dimensions.space50,
                width: 1,
                color: MyColor.getBorderColor(),
                margin: const EdgeInsets.symmetric(horizontal: 10),
              ),
              Expanded(
                child: CardColumn(
                  headerTextStyle: MyTextStyle.caption1Style.copyWith(
                    color: MyColor.getBodyTextColor(),
                  ),
                  bodyTextStyle: MyTextStyle.sectionTitle3.copyWith(
                    fontSize: Dimensions.fontLarge,
                    color: MyColor.getHeaderTextColor(),
                  ),
                  header: MyStrings.charge,
                  body: MyUtils.getUserAmount(item.charge ?? ""),
                  space: 5,
                  crossAxisAlignment: CrossAxisAlignment.end,
                ),
              ),
            ],
          ),
          spaceDown(Dimensions.space10),
          Container(
            height: 1,
            width: double.infinity,
            color: MyColor.getBorderColor(),
            margin: const EdgeInsets.symmetric(horizontal: 10),
          ),
          spaceDown(Dimensions.space10),
          //Total  and trx
          Row(
            children: [
              Expanded(
                child: CardColumn(
                  headerTextStyle: MyTextStyle.caption1Style.copyWith(
                    color: MyColor.getBodyTextColor(),
                  ),
                  bodyTextStyle: MyTextStyle.sectionTitle3.copyWith(
                    fontSize: Dimensions.fontLarge,
                    color: MyColor.getHeaderTextColor(),
                  ),
                  header: MyStrings.total.tr,
                  body: MyUtils.getUserAmount(item.totalAmount ?? ""),
                  space: 5,
                  crossAxisAlignment: CrossAxisAlignment.start,
                ),
              ),
              Container(
                height: Dimensions.space50,
                width: 1,
                color: MyColor.getBorderColor(),
                margin: const EdgeInsets.symmetric(horizontal: 10),
              ),
              Expanded(
                child: CardColumn(
                  headerTextStyle: MyTextStyle.caption1Style.copyWith(
                    color: MyColor.getBodyTextColor(),
                  ),
                  bodyTextStyle: MyTextStyle.sectionTitle3.copyWith(
                    fontSize: Dimensions.fontLarge,
                    color: MyColor.getHeaderTextColor(),
                  ),
                  header: MyStrings.transactionId.tr,
                  body: item.trx ?? "---",
                  space: 5,
                  isCopyable: true,
                  crossAxisAlignment: CrossAxisAlignment.end,
                ),
              ),
            ],
          ),
          spaceDown(Dimensions.space10),
          Container(
            height: 1,
            width: double.infinity,
            color: MyColor.getBorderColor(),
            margin: const EdgeInsets.symmetric(horizontal: 10),
          ),
          spaceDown(Dimensions.space10),
          //Note
          Row(
            children: [buildTransactionHeaderInfo(type: "note_details")],
          ),
        ],
      ),
    );
  }

  Widget buildTransactionHeaderInfo({String type = "header_info"}) {
    if (type == "header_info") {
      return item.otherData?.title == null
          ? SizedBox.shrink()
          : CustomCompanyListTileCard(
              leading: MyNetworkImageWidget(
                width: Dimensions.space40.w,
                height: Dimensions.space40.w,
                imageAlt: item.otherData?.title ?? "",
                isProfile: true,
                boxFit: BoxFit.contain,
                imageUrl: item.otherData?.imageSrc ?? "",
              ),
              showBorder: false,
              imagePath: item.otherData?.imageSrc ?? "",
              title: item.otherData?.title?.cleanAndTitleCase(),
              subtitle: item.otherData?.subtitle?.cleanAndTitleCase(),
            );
    } else if (type == "account_info") {
      var header = MyStrings.account.tr;
      var body = item.otherData?.title?.cleanAndTitleCase();
      var subBody = item.otherData?.subtitle?.cleanAndTitleCase();
      if (["donation", "requested_money_fund_added"].contains(item.remark)) {
        header = MyStrings.note.tr;
        body = null;
        subBody = item.otherData?.note ?? "";
      }
      return Expanded(
        child: CardColumn(
          headerTextStyle: MyTextStyle.caption1Style.copyWith(
            color: MyColor.getBodyTextColor(),
          ),
          bodyTextStyle: MyTextStyle.sectionTitle3.copyWith(
            fontSize: Dimensions.fontLarge,
            color: MyColor.getHeaderTextColor(),
          ),
          header: header,
          body: body,
          subBody: subBody,
          space: 5,
          crossAxisAlignment: CrossAxisAlignment.start,
        ),
      );
    } else if (type == "note_details") {
      var header = MyStrings.note.tr;
      var subBody = "";

      if ([
        "reject_bank_transfer",
        "reject_mobile_recharge",
        "reject_microfinance",
        "reject_education_fee",
        "reject_utility_bill",
      ].contains(item.remark)) {
        header = MyStrings.adminFeedback.tr;
        subBody = item.otherData?.feedback ?? "";
      }

      if (subBody != "") {
        return Expanded(
          child: CardColumn(
            headerTextStyle: MyTextStyle.caption1Style.copyWith(
              color: MyColor.getBodyTextColor(),
            ),
            bodyTextStyle: MyTextStyle.sectionTitle3.copyWith(
              fontSize: Dimensions.fontLarge,
              color: MyColor.getHeaderTextColor(),
            ),
            header: header,
            subBody: subBody,
            space: 5,
            crossAxisAlignment: CrossAxisAlignment.start,
          ),
        );
      }
    }

    return SizedBox.shrink(child: Text(MyStrings.noData.tr));
  }
}
