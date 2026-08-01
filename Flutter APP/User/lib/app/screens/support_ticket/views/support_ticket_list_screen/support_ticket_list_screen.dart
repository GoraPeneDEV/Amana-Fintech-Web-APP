import 'package:flutter/material.dart';

import 'package:get/get.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/app/components/custom_loader/custom_loader.dart';
import 'package:ovopay/app/components/floating_action_button/fab.dart';
import 'package:ovopay/app/components/no_data.dart';
import 'package:ovopay/app/components/shimmer/all_ticket_shimmer.dart';
import 'package:ovopay/app/screens/support_ticket/views/support_ticket_list_screen/widget/all_ticket_list_item.dart';
import 'package:ovopay/app/screens/support_ticket/controller/support_controller.dart';
import 'package:ovopay/core/data/repositories/support/support_repo.dart';
import 'package:ovopay/core/route/route.dart';

import '../../../../../core/utils/util_exporter.dart';

class SupportTicketListScreen extends StatefulWidget {
  const SupportTicketListScreen({super.key});

  @override
  State<SupportTicketListScreen> createState() => _SupportTicketListScreenState();
}

class _SupportTicketListScreenState extends State<SupportTicketListScreen> {
  ScrollController scrollController = ScrollController();

  void scrollListener() {
    if (scrollController.position.pixels == scrollController.position.maxScrollExtent) {
      if (Get.find<SupportController>().hasNext()) {
        Get.find<SupportController>().getSupportTicket();
      }
    }
  }

  @override
  void initState() {
    Get.put(SupportRepo());
    final controller = Get.put(SupportController(repo: Get.find()));
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((timeStamp) {
      controller.loadData();
      scrollController.addListener(scrollListener);
    });
  }

  @override
  Widget build(BuildContext context) {
    return GetBuilder<SupportController>(
      builder: (controller) {
        return MyCustomScaffold(
          pageTitle: MyStrings.supportTicket,
          body: RefreshIndicator(
            onRefresh: () async {
              controller.loadData();
            },
            color: MyColor.getPrimaryColor(),
            child: controller.isLoading
                ? ListView.separated(
                    itemCount: 10,
                    physics: const AlwaysScrollableScrollPhysics(
                      parent: ClampingScrollPhysics(),
                    ),
                    shrinkWrap: true,
                    separatorBuilder: (context, index) => Container(
                      margin: const EdgeInsets.only(
                        bottom: Dimensions.space10,
                      ),
                    ),
                    itemBuilder: (BuildContext context, int index) {
                      return const TicketShimmer();
                    },
                  )
                : controller.ticketList.isEmpty
                    ? CustomAppCard(
                        child: SingleChildScrollView(
                          child: NoDataWidget(
                            text: MyStrings.noTransactionsToShow.tr,
                          ),
                        ),
                      )
                    : ListView.separated(
                        padding: EdgeInsets.zero,
                        controller: scrollController,
                        itemCount: controller.ticketList.length + 1,
                        shrinkWrap: true,
                        physics: const AlwaysScrollableScrollPhysics(
                          parent: ClampingScrollPhysics(),
                        ),
                        separatorBuilder: (context, index) => const SizedBox(height: Dimensions.space10),
                        itemBuilder: (context, index) {
                          if (controller.ticketList.length == index) {
                            return controller.hasNext() ? const CustomLoader(isPagination: true) : const SizedBox();
                          }
                          return AllTicketListItem(
                            onPress: () {
                              String id = controller.ticketList[index].ticket ?? '-1';
                              String subject = controller.ticketList[index].subject ?? '';
                              Get.toNamed(
                                RouteHelper.ticketDetailsScreen,
                                arguments: [id, subject],
                              )?.then((v) {
                                controller.loadData(forceLoad: false);
                              });
                            },
                            ticketNumber: controller.ticketList[index].ticket ?? '',
                            subject: controller.ticketList[index].subject ?? '',
                            status: TicketHelper.getStatusText(
                              controller.ticketList[index].status ?? '0',
                            ),
                            priority: TicketHelper.getPriorityText(
                              controller.ticketList[index].priority ?? '0',
                            ),
                            statusColor: TicketHelper.getStatusColor(
                              controller.ticketList[index].status ?? '0',
                            ),
                            priorityColor: TicketHelper.getPriorityColor(
                              controller.ticketList[index].priority ?? '0',
                            ),
                            time: DateConverter.getTimeAgo(
                              controller.ticketList[index].createdAt ?? '',
                            ),
                          );
                        },
                      ),
          ),
          floatingActionButton: FAB(
            icon: Icon(Icons.add, color: MyColor.getPrimaryColor()),
            callback: () {
              Get.toNamed(RouteHelper.newTicketScreen)?.then(
                (value) => {
                  if (value != null && value == 'updated') {controller.loadData()},
                },
              );
            },
          ),
        );
      },
    );
  }
}
