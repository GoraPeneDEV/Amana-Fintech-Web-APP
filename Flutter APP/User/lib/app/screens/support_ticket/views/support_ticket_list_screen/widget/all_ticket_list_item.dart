import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/badges/priority_badge.dart';
import 'package:ovopay/app/components/badges/status_badge.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/column_widget/card_column.dart';

import '../../../../../../core/utils/util_exporter.dart';

class AllTicketListItem extends StatelessWidget {
  final String ticketNumber;
  final String subject;
  final String status;
  final Color statusColor;
  final String priority;
  final Color priorityColor;
  final String time;
  final VoidCallback? onPress;

  const AllTicketListItem({
    super.key,
    required this.ticketNumber,
    required this.subject,
    required this.status,
    required this.priority,
    required this.statusColor,
    required this.priorityColor,
    required this.time,
    this.onPress,
  });

  @override
  Widget build(BuildContext context) {
    return CustomAppCard(
      onPressed: onPress,
      child: Column(
        children: [
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Flexible(
                child: Padding(
                  padding: const EdgeInsetsDirectional.only(
                    end: Dimensions.space10,
                  ),
                  child: Column(
                    children: [
                      CardColumn(
                        header: "[${MyStrings.ticket.tr}#$ticketNumber]",
                        body: subject.tr,
                        space: 5,
                        headerTextStyle: MyTextStyle.sectionTitle.copyWith(
                          color: MyColor.getHeaderTextColor(),
                        ),
                        bodyTextStyle: MyTextStyle.sectionSubTitle1.copyWith(
                          color: MyColor.getBodyTextColor(),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
              StatusBadge(text: status, color: statusColor),
            ],
          ),
          const SizedBox(height: Dimensions.space15),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              PriorityBadge(text: priority, color: priorityColor),
              Text(
                time,
                style: MyTextStyle.sectionSubTitle1.copyWith(color: MyColor.getBodyTextColor()).copyWith(
                      fontSize: Dimensions.fontSmall,
                      fontStyle: FontStyle.italic,
                      color: MyColor.getBodyTextColor(),
                    ),
              ),
            ],
          ),
        ],
      ),
    );
  }
}
