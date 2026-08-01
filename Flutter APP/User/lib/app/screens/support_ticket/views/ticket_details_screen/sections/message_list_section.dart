import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/no_data.dart';
import 'package:ovopay/app/screens/support_ticket/controller/ticket_details_controller.dart';

import '../../../../../../core/utils/util_exporter.dart';
import '../widget/ticket_list_item.dart';

class MessageListSection extends StatelessWidget {
  const MessageListSection({super.key});

  @override
  Widget build(BuildContext context) {
    return GetBuilder<TicketDetailsController>(
      builder: (controller) => controller.messageList.isEmpty
          ? FittedBox(
              fit: BoxFit.scaleDown,
              child: NoDataWidget(text: MyStrings.noMSgFound.tr),
            )
          : ListView.builder(
              clipBehavior: Clip.none,
              itemCount: controller.messageList.length,
              shrinkWrap: true,
              itemBuilder: (context, index) => TicketListItem(
                index: index,
                messages: controller.messageList[index],
              ),
            ),
    );
  }
}
