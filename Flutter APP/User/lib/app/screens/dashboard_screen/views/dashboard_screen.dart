import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/annotated_region/annotated_region_widget.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/app/components/will_pop_widget.dart';
import 'package:ovopay/app/packages/google_nav_bar/google_nav_bar.dart';
import 'package:ovopay/app/screens/dashboard_screen/controller/home_controller.dart';
import 'package:ovopay/app/screens/dashboard_screen/views/home_screen.dart';
import 'package:ovopay/app/screens/dashboard_screen/views/widgets/drawer_screen.dart';
import 'package:ovopay/app/screens/profile_and_settings_screen/views/profile_and_settings_screen.dart';
import 'package:ovopay/app/screens/statements/controller/statement_history_controller.dart';
import 'package:ovopay/app/screens/statements/statement_screen.dart';
import 'package:ovopay/app/screens/transaction_history/controller/transaction_history_controller.dart';
import 'package:ovopay/app/screens/transaction_history/views/transaction_history_screen.dart';
import 'package:ovopay/core/utils/util_exporter.dart';

class DashboardScreen extends StatefulWidget {
  const DashboardScreen({super.key});

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  final GlobalKey<ScaffoldState> _dashboardKey = GlobalKey(); // Create a key

  int _selectedIndex = 0;

  List<Widget> _pages = [];

  void _onItemTapped(int index) {
    setState(() {
      _selectedIndex = index;
    });

    if (index == 0) {
      try {
        Get.find<HomeController>().initController(forceLoad: false);
      } catch (e) {
        printE(e.toString());
      }
      return;
    }
    if (index == 1) {
      try {
        Get.find<TransactionHistoryController>().resetFilter();
      } catch (e) {
        printE(e.toString());
      }
      return;
    }
    if (index == 2) {
      try {
        Get.find<StatementHistoryController>().initialHistoryData(
          forceLoad: false,
        );
      } catch (e) {
        printE(e.toString());
      }
      return;
    }
  }

  @override
  void initState() {
    _pages = [
      HomeScreen(
        dashboardKey: _dashboardKey,
        onViewAllTransactionTapped: (index) {
          _onItemTapped(index);
        },
      ),
      TransactionHistoryScreen(
        onItemTapped: (index) {
          _onItemTapped(index);
        },
      ),
      StatementScreen(
        onItemTapped: (index) {
          _onItemTapped(index);
        },
      ),
      ProfileAndSettingsScreen(
        onItemTapped: (index) {
          _onItemTapped(index);
        },
      ),
    ];
    super.initState();
  }

  @override
  Widget build(BuildContext context) {
    return WillPopWidget(
      nextRoute: "",
      child: AnnotatedRegionWidget(
        statusBarColor: MyColor.white,
        systemNavigationBarColor: MyColor.white,
        child: Scaffold(
          key: _dashboardKey,
          drawer: DrawerScreen(dashboardKey: _dashboardKey),
          backgroundColor: MyColor.getScreenBgColor(),
          body: IndexedStack(index: _selectedIndex, children: _pages),
          bottomNavigationBar: GNav(
            tabMargin: EdgeInsets.symmetric(
              vertical: Dimensions.space16.w,
              horizontal: Dimensions.space16.w,
            ),
            gap: Dimensions.space4.w,
            color: MyColor.getBodyTextColor(),
            activeColor: MyColor.getPrimaryColor(),
            backgroundColor: Colors.white,
            tabBorderRadius: Dimensions.largeRadius.r,
            tabBackgroundColor: MyColor.getPrimaryColor().withValues(
              alpha: 0.1,
            ),
            padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
            onTabChange: _onItemTapped,
            selectedIndex: _selectedIndex,
            tabs: [
              GButton(
                leading: MyAssetImageWidget(
                  isSvg: true,
                  width: Dimensions.space24.h,
                  height: Dimensions.space24.h,
                  boxFit: BoxFit.scaleDown,
                  color: _selectedIndex == 0 ? MyColor.getPrimaryColor() : MyColor.getBodyTextColor(),
                  assetPath: _selectedIndex == 0 ? MyIcons.homeActive : MyIcons.homeInactive,
                ),
                icon: Icons.home,
                text: MyStrings.home.tr,
              ),
              GButton(
                leading: MyAssetImageWidget(
                  isSvg: true,
                  width: Dimensions.space24.h,
                  height: Dimensions.space24.h,
                  boxFit: BoxFit.scaleDown,
                  color: _selectedIndex == 1 ? MyColor.getPrimaryColor() : MyColor.getBodyTextColor(),
                  assetPath: _selectedIndex == 1 ? MyIcons.historyActive : MyIcons.historyInactive,
                ),
                icon: Icons.history,
                text: MyStrings.history.tr,
              ),
              GButton(
                leading: MyAssetImageWidget(
                  isSvg: true,
                  width: Dimensions.space24.h,
                  height: Dimensions.space24.h,
                  boxFit: BoxFit.scaleDown,
                  color: _selectedIndex == 2 ? MyColor.getPrimaryColor() : MyColor.getBodyTextColor(),
                  assetPath: _selectedIndex == 2 ? MyIcons.statementActive : MyIcons.statementInactive,
                ),
                icon: Icons.notes_outlined,
                text: MyStrings.statement.tr,
              ),
              GButton(
                leading: MyAssetImageWidget(
                  isSvg: true,
                  width: Dimensions.space24.h,
                  height: Dimensions.space24.h,
                  boxFit: BoxFit.scaleDown,
                  color: _selectedIndex == 3 ? MyColor.getPrimaryColor() : MyColor.getBodyTextColor(),
                  assetPath: _selectedIndex == 3 ? MyIcons.profileActive : MyIcons.profileInactive,
                ),
                icon: Icons.person,
                text: MyStrings.profile.tr,
              ),
            ],
          ),
        ),
      ),
    );
  }
}
