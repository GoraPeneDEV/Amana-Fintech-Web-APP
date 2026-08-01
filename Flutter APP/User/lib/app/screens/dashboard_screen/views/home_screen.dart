import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/shimmer/home_shimmer.dart';
import 'package:ovopay/app/screens/dashboard_screen/controller/home_controller.dart';
import 'package:ovopay/app/screens/dashboard_screen/views/widgets/home_screen_appbar.dart';
import 'package:ovopay/app/screens/dashboard_screen/views/widgets/home_screen_balance_card.dart';
import 'package:ovopay/app/screens/dashboard_screen/views/widgets/home_screen_banner_card.dart';
import 'package:ovopay/app/screens/dashboard_screen/views/widgets/home_screen_kyc_status_card.dart';
import 'package:ovopay/app/screens/dashboard_screen/views/widgets/home_screen_payment_offer_list_card.dart';
import 'package:ovopay/app/screens/dashboard_screen/views/widgets/home_screen_service_menu_card.dart';
import 'package:ovopay/app/screens/dashboard_screen/views/widgets/home_screen_transaction_list_card.dart';
import 'package:ovopay/core/utils/util_exporter.dart';

class HomeScreen extends StatefulWidget {
  final GlobalKey<ScaffoldState> dashboardKey;
  final Function(int index)? onViewAllTransactionTapped;
  const HomeScreen({
    super.key,
    required this.dashboardKey,
    this.onViewAllTransactionTapped,
  });

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  @override
  void initState() {
    super.initState();

    final controller = Get.put(HomeController());

    // Fetch initial data
    WidgetsBinding.instance.addPostFrameCallback((_) async {
      if (mounted) {
        controller.initController();
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return GetBuilder<HomeController>(
      builder: (homeController) {
        return Scaffold(
          appBar: HomePageAppBar(dashboardKey: widget.dashboardKey),
          body: homeController.isLoading
              ? HomeShimmer()
              : RefreshIndicator(
                  color: MyColor.getPrimaryColor(),
                  onRefresh: () async {
                    homeController.initController();
                  },
                  child: ListView(
                    padding: EdgeInsetsDirectional.all(Dimensions.space16.w),
                    children: [
                      //kyc
                      HomeScreenKycStatusCard(),
                      //Balance Card
                      HomeScreenBalanceCard(),

                      if (homeController.isLoading == false) ...[
                        //Service menu
                        HomeScreenServiceMenuCard(),
                      ],
                      //Banner Card
                      HomeScreenBannerCard(),
                      //Payment Offers
                      if (homeController.offersList.isNotEmpty) ...[
                        spaceDown(Dimensions.space20),
                      ],
                      HomeScreenPaymentOffersCard(),
                      //Transaction
                      spaceDown(Dimensions.space20),
                      HomeScreenTransactionMenuCard(
                        onViewAllTransactionTapped: widget.onViewAllTransactionTapped,
                      ),
                    ],
                  ),
                ),
        );
      },
    );
  }
}
