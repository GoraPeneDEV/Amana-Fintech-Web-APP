import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/app/screens/send_money_screen/views/widgets/send_money_amount_page.dart';
import 'package:ovopay/app/screens/send_money_screen/views/widgets/send_money_contact_select_page.dart';
import 'package:ovopay/app/screens/send_money_screen/views/widgets/send_money_pin_page.dart';
import 'package:ovopay/core/data/controller/contact/contact_controller.dart';
import 'package:ovopay/core/data/models/global/qr_code/scan_qr_code_response_model.dart';
import 'package:ovopay/core/data/models/user/user_model.dart';
import 'package:ovopay/core/data/repositories/modules/send_money/send_money_repo.dart';
import 'package:ovopay/core/route/route.dart';
import '../../../../../core/utils/util_exporter.dart';
import '../controller/send_money_controller.dart';

class SendMoneyScreen extends StatefulWidget {
  const SendMoneyScreen({super.key, this.toUserInformation});
  final UserModel? toUserInformation;

  @override
  State<SendMoneyScreen> createState() => _SendMoneyScreenState();
}

class _SendMoneyScreenState extends State<SendMoneyScreen> {
  final PageController _pageController = PageController();
  int _currentPage = 0;

  @override
  void initState() {
    Get.put(SendMoneyRepo());
    final controller = Get.put(SendMoneyController(sendMoneyRepo: Get.find()));

    super.initState();
    // Add listener to track page changes
    _pageController.addListener(_pageChangeListener);

    // Fetch contacts on app startup
    ContactController.to.requestPermissions();

    WidgetsBinding.instance.addPostFrameCallback((v) async {
      await controller.initController();
      if (widget.toUserInformation != null) {
        controller.setUserFromQrScan(() {
          _nextPage(goToPage: 1);
        }, existUserDataModel: widget.toUserInformation);
      }
    });
  }

  void _pageChangeListener() {
    int newPage = _pageController.page?.round() ?? 0;
    if (newPage != _currentPage) {
      setState(() {
        _currentPage = newPage;
      });
    }
  }

  @override
  void dispose() {
    // Remove listener when the widget is disposed to avoid memory leaks
    _pageController.removeListener(_pageChangeListener);
    _pageController.dispose();
    super.dispose();
  }

  void _nextPage({int? goToPage}) {
    setState(() {
      _pageController.animateToPage(
        goToPage ?? ++_currentPage,
        duration: const Duration(milliseconds: 600),
        curve: Curves.easeIn,
      );
    });
  }

  void _previousPage({int? goToPage}) {
    setState(() {
      _pageController.animateToPage(
        goToPage ?? --_currentPage,
        duration: const Duration(milliseconds: 600),
        curve: Curves.easeIn,
      );
    });
  }

  @override
  Widget build(BuildContext context) {
    return MyCustomScaffold(
      pageTitle: MyStrings.sendMoney,
      onBackButtonTap: () {
        if (_currentPage != 0) {
          _previousPage();
        } else {
          Get.back();
        }
      },
      actionButton: [
        if (_currentPage == 0)
          CustomAppCard(
            onPressed: () {
              Get.toNamed(RouteHelper.sendMoneyHistoryScreen);
            },
            width: Dimensions.space40.w,
            height: Dimensions.space40.w,
            padding: EdgeInsetsDirectional.all(Dimensions.space8.w),
            radius: Dimensions.largeRadius.r,
            child: MyAssetImageWidget(
              isSvg: true,
              assetPath: MyIcons.historyInactive,
              width: Dimensions.space24.h,
              height: Dimensions.space24.h,
              color: MyColor.getPrimaryColor(),
            ),
          ),
        spaceSide(Dimensions.space16.w),
      ],
      body: PageView(
        clipBehavior: Clip.none,
        controller: _pageController,
        physics: NeverScrollableScrollPhysics(),
        children: [
          SendMoneyContactSelectPage(
            onSuccessCallback: () {
              _nextPage(goToPage: 1);
            },
          ),
          SendMoneyAmountPage(
            onSuccessCallback: () {
              _nextPage(goToPage: 2);
            },
            context: context,
          ),
          SendMoneyPinVerificationPage(context: context),
        ],
      ),
      floatingActionButton: _currentPage == 0
          ? FloatingActionButton(
              backgroundColor: MyColor.getWhiteColor(),
              onPressed: () {
                Get.toNamed(
                  RouteHelper.scanQrCodeScreen,
                  arguments: MyStrings.scanUserQrCode,
                )?.then((v) {
                  ScanQrCodeResponseModel scanQrCodeResponseModel = v as ScanQrCodeResponseModel;
                  if (scanQrCodeResponseModel.data?.userType != AppStatus.USER_TYPE_USER) {
                    CustomSnackBar.error(
                      errorList: [MyStrings.pleaseScanUserQRCode],
                    );
                    return;
                  }
                  Get.find<SendMoneyController>().checkUserExist(
                    inputUserNameOrPhone: scanQrCodeResponseModel.data?.userData?.username ?? "",
                    () {
                      _nextPage(goToPage: 1);
                    },
                  );
                });
              },
              child: Padding(
                padding: const EdgeInsets.all(8.0),
                child: MyAssetImageWidget(
                  isSvg: true,
                  assetPath: MyIcons.walletQrCodeIcon,
                  color: MyColor.getPrimaryColor(),
                ),
              ),
            )
          : null,
    );
  }
}
