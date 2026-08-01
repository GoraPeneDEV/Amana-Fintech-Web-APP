import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/badges/status_badge.dart';
import 'package:ovopay/app/components/column_widget/card_column.dart';
import 'package:ovopay/app/screens/support_ticket/controller/ticket_details_controller.dart';

import '../../../../../../core/utils/util_exporter.dart';

class TicketStatusWidget extends StatelessWidget {
  final TicketDetailsController controller;

  const TicketStatusWidget({super.key, required this.controller});

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(
        bottom: Dimensions.space20,
        top: Dimensions.space5,
      ),
      padding: const EdgeInsets.symmetric(horizontal: Dimensions.space5),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        crossAxisAlignment: CrossAxisAlignment.center,
        children: [
          Expanded(
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Expanded(
                  child: CardColumn(
                    isOnlyHeader: false,
                    header: "[${MyStrings.ticket.tr}#${controller.model.data?.myTickets?.ticket ?? ''}]",
                    body: controller.model.data?.myTickets?.subject ?? '',
                    bodyMaxLine: 3,
                    space: 7,
                    headerTextStyle: MyTextStyle.sectionTitle3.copyWith(
                      color: MyColor.getHeaderTextColor(),
                    ),
                    bodyTextStyle: MyTextStyle.caption1Style.copyWith(
                      color: MyColor.getBodyTextColor(),
                    ),
                  ),
                ),
                StatusBadge(
                  text: TicketHelper.getStatusText(
                    controller.model.data?.myTickets?.status ?? '0',
                  ),
                  color: TicketHelper.getStatusColor(
                    controller.model.data?.myTickets?.status ?? "0",
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
