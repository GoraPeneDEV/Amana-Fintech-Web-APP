import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/app/components/divider/custom_divider.dart';
import 'package:ovopay/app/components/text/header_text.dart';
import 'package:ovopay/app/screens/request_money_screen/views/widgets/sent_request_tab_widget.dart';
import 'package:ovopay/app/screens/request_money_screen/views/widgets/received_request_tab_widget.dart';
import 'package:ovopay/core/data/repositories/modules/request_money/request_money_repo.dart';

import '../../../../../core/utils/util_exporter.dart';
import '../controller/request_money_controller.dart';

class RequestMoneyHistoryScreen extends StatefulWidget {
  const RequestMoneyHistoryScreen({super.key, this.isMyRequested = true});
  final bool isMyRequested;

  @override
  State<RequestMoneyHistoryScreen> createState() => _RequestMoneyHistoryScreenState();
}

class _RequestMoneyHistoryScreenState extends State<RequestMoneyHistoryScreen> with SingleTickerProviderStateMixin {
  late TabController _tabController;
  late int _selectedIndex;

  final ScrollController receiverScrollController = ScrollController();
  final ScrollController senderScrollController = ScrollController();

  void fetchData({required bool isReceiver}) {
    Get.find<RequestMoneyController>().getHistoryData(
      isReceiver: isReceiver,
      forceLoad: false,
    );
  }

  void scrollListener({required bool isReceiver}) {
    final scrollController = isReceiver ? receiverScrollController : senderScrollController;
    if (scrollController.position.pixels == scrollController.position.maxScrollExtent) {
      if (Get.find<RequestMoneyController>().hasNext(isReceiver)) {
        fetchData(isReceiver: isReceiver);
      }
    }
  }

  @override
  void initState() {
    super.initState();

    // Initialize RequestMoneyController
    Get.put(RequestMoneyRepo());
    final controller = Get.put(
      RequestMoneyController(requestMoneyRepo: Get.find()),
    );

    // Set initial tab index
    _selectedIndex = widget.isMyRequested ? 0 : 1;
    _tabController = TabController(
      length: 2,
      vsync: this,
      initialIndex: _selectedIndex,
    );

    // Tab change listener
    _tabController.addListener(() {
      if (!_tabController.indexIsChanging) {
        setState(() {
          _selectedIndex = _tabController.index;
        });
      }
    });

    // Fetch initial data
    WidgetsBinding.instance.addPostFrameCallback((_) async {
      if (mounted) {
        controller.initialHistoryData(
          true,
        ); // Receiver if index is 0, Sender otherwise
        controller.initialHistoryData(
          false,
        ); // Receiver if index is 0, Sender otherwise

        // Add scroll listeners
        receiverScrollController.addListener(
          () => scrollListener(isReceiver: true),
        );
        senderScrollController.addListener(
          () => scrollListener(isReceiver: false),
        );
      }
    });
  }

  @override
  void dispose() {
    _tabController.dispose();
    receiverScrollController.dispose();
    senderScrollController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return MyCustomScaffold(
      pageTitle: MyStrings.requestMoney,
      body: CustomAppCard(
        padding: EdgeInsets.zero,
        width: double.infinity,
        child: DefaultTabController(
          length: 2,
          child: Column(
            children: [
              // Custom Tab Bar
              Padding(
                padding: EdgeInsets.all(16.w),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    _buildAnimatedTab(0, MyStrings.receivedRequest.tr),
                    spaceSide(Dimensions.space10),
                    _buildAnimatedTab(1, MyStrings.sentRequest.tr),
                  ],
                ),
              ),
              //Border
              CustomDivider(
                color: MyColor.getBorderColor(),
                space: 0,
                thickness: 1,
              ),

              // TabBarView Content
              Expanded(
                child: TabBarView(
                  controller: _tabController,
                  children: [
                    ReceivedRequestTabWidget(
                      scrollController: receiverScrollController,
                    ),
                    SentRequestTabWidget(
                      scrollController: senderScrollController,
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildAnimatedTab(int index, String label) {
    bool isSelected = _selectedIndex == index;

    return Expanded(
      child: GestureDetector(
        onTap: () {
          _tabController.animateTo(index);
        },
        child: AnimatedContainer(
          duration: Duration(milliseconds: 200),
          curve: Curves.easeInOut,
          padding: EdgeInsets.symmetric(
            vertical: Dimensions.space15.h,
            horizontal: Dimensions.space10.w,
          ),
          decoration: BoxDecoration(
            color: isSelected ? MyColor.getPrimaryColor().withValues(alpha: 1) : MyColor.getScreenBgColor(),
            borderRadius: BorderRadius.circular(Dimensions.largeRadius.r),
          ),
          child: FittedBox(
            fit: BoxFit.scaleDown,
            child: HeaderText(
              text: label,
              textAlign: TextAlign.center,
              textStyle: MyTextStyle.sectionTitle.copyWith(
                color: isSelected ? MyColor.getWhiteColor() : MyColor.getBodyTextColor(),
              ),
            ),
          ),
        ),
      ),
    );
  }
}
