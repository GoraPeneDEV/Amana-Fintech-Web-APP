import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/app/components/custom_loader/custom_loader.dart';
import 'package:ovopay/app/components/no_data.dart';
import 'package:ovopay/app/components/shimmer/transaction_history_shimmer.dart';
import 'package:ovopay/app/components/text/default_text.dart';
import 'package:ovopay/app/components/text/header_text.dart';
import 'package:ovopay/app/screens/notification_screen/controller/notification_history_controller.dart';
import 'package:ovopay/core/data/repositories/modules/notification/notification_history_repo.dart';

import '../../../../../core/utils/util_exporter.dart';

class NotificationScreen extends StatefulWidget {
  const NotificationScreen({super.key});

  @override
  State<NotificationScreen> createState() => _NotificationScreenState();
}

class _NotificationScreenState extends State<NotificationScreen> {
  final ScrollController historyScrollController = ScrollController();
  void fetchData() {
    Get.find<NotificationHistoryController>().getNotificationHistoryDataList(
      forceLoad: false,
    );
  }

  void scrollListener() {
    if (historyScrollController.position.pixels == historyScrollController.position.maxScrollExtent) {
      if (Get.find<NotificationHistoryController>().hasNext()) {
        fetchData();
      }
    }
  }

  @override
  void initState() {
    super.initState();

    Get.put(NotificationRepo());
    final controller = Get.put(
      NotificationHistoryController(notificationRepo: Get.find()),
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
    return GetBuilder<NotificationHistoryController>(
      builder: (controller) {
        return MyCustomScaffold(
          pageTitle: MyStrings.notification,
          actionButton: [],
          padding: EdgeInsetsDirectional.symmetric(
            horizontal: Dimensions.space16.w,
          ),
          body: controller.isHistoryLoading
              ? CustomAppCard(
                  margin: EdgeInsetsDirectional.only(
                    top: Dimensions.space16,
                    bottom: Dimensions.space16,
                  ),
                  padding: EdgeInsets.symmetric(
                    horizontal: Dimensions.space16.w,
                  ),
                  child: TransactionHistoryShimmer(),
                )
              : (controller.notificationHistoryList.isEmpty)
                  ? SingleChildScrollView(
                      child: NoDataWidget(
                        text: MyStrings.noNotificationToShow.tr,
                      ),
                    )
                  : RefreshIndicator(
                      color: MyColor.getPrimaryColor(),
                      onRefresh: () async {
                        controller.initialHistoryData();
                      },
                      child: ListView.builder(
                        controller: historyScrollController,
                        itemCount: controller.notificationHistoryList.length + 1,
                        padding: EdgeInsetsDirectional.only(
                          top: Dimensions.space16,
                          bottom: Dimensions.space16,
                        ),
                        itemBuilder: (context, index) {
                          if (controller.notificationHistoryList.length == index) {
                            return controller.hasNext() ? const CustomLoader(isPagination: true) : const SizedBox();
                          }
                          var item = controller.notificationHistoryList[index];
                          return Stack(
                            children: [
                              CustomAppCard(
                                width: double.infinity,
                                margin: EdgeInsetsDirectional.symmetric(
                                  vertical: Dimensions.space5,
                                ),
                                padding: EdgeInsetsDirectional.symmetric(
                                  vertical: Dimensions.space10,
                                  horizontal: Dimensions.space10,
                                ),
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(
                                      DateConverter.estimatedDateOrTime(
                                        item.createdAt ?? "",
                                        customFormat: "dd-MM-yyyy hh:mm aa",
                                      ),
                                      style: MyTextStyle.sectionSubTitle1.copyWith(
                                        color: MyColor.getBodyTextColor(),
                                      ),
                                    ),
                                    spaceDown(Dimensions.space3),
                                    HeaderText(text: item.subject ?? ""),
                                    spaceDown(Dimensions.space3),
                                    DefaultText(
                                      text: item.message ?? "",
                                      textStyle: MyTextStyle.sectionSubTitle1.copyWith(
                                        color: MyColor.getBodyTextColor(),
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                            ],
                          );
                        },
                      ),
                    ),
        );
      },
    );
  }
}
