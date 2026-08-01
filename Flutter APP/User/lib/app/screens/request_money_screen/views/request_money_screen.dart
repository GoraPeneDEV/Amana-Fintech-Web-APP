import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/app/components/text/default_text.dart';
import 'package:ovopay/app/screens/request_money_screen/views/widgets/request_money_contact_select_page.dart';
import 'package:ovopay/app/screens/request_money_screen/views/widgets/request_money_pin_page.dart';
import 'package:ovopay/app/screens/request_money_screen/views/widgets/request_money_amount_page.dart';
import 'package:ovopay/core/data/controller/contact/contact_controller.dart';
import 'package:ovopay/core/data/models/global/qr_code/scan_qr_code_response_model.dart';
import 'package:ovopay/core/data/repositories/modules/request_money/request_money_repo.dart';
import 'package:ovopay/core/route/route.dart';

import '../../../../../core/utils/util_exporter.dart';
import '../controller/request_money_controller.dart';

class RequestMoneyScreen extends StatefulWidget {
  const RequestMoneyScreen({super.key});

  @override
  State<RequestMoneyScreen> createState() => _RequestMoneyScreenState();
}

class _RequestMoneyScreenState extends State<RequestMoneyScreen> {
  final PageController _pageController = PageController();
  int _currentPage = 0;

  @override
  void initState() {
    Get.put(RequestMoneyRepo());
    final controller = Get.put(
      RequestMoneyController(requestMoneyRepo: Get.find()),
    );

    super.initState();
    // Add listener to track page changes
    _pageController.addListener(_pageChangeListener);

    // Fetch contacts on app startup
    ContactController.to.requestPermissions();

    WidgetsBinding.instance.addPostFrameCallback((v) async {
      await controller.initController();
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
      pageTitle: MyStrings.requestMoney,
      actionButton: [
        if (_currentPage == 0)
          GetBuilder<RequestMoneyController>(
            builder: (reqController) {
              return Stack(
                clipBehavior: Clip.none,
                children: [
                  CustomAppCard(
                    onPressed: () {
                      Get.toNamed(RouteHelper.requestMoneyHistoryScreen);
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
                  if (reqController.pendingRequestCounter != "0") ...[
                    PositionedDirectional(
                      end: -Dimensions.space5,
                      top: -Dimensions.space5,
                      child: CustomAppCard(
                        width: Dimensions.space20.w,
                        height: Dimensions.space20.w,
                        radius: Dimensions.radiusProMax,
                        showBorder: false,
                        padding: EdgeInsets.all(0),
                        backgroundColor: MyColor.getPrimaryColor(),
                        child: FittedBox(
                          fit: BoxFit.scaleDown,
                          child: Center(
                            child: DefaultText(
                              text: reqController.pendingRequestCounter,
                              textStyle: MyTextStyle.caption2Style.copyWith(
                                color: MyColor.getWhiteColor(),
                              ),
                            ),
                          ),
                        ),
                      ),
                    ),
                  ],
                ],
              );
            },
          ),
        spaceSide(Dimensions.space16.w),
      ],
      onBackButtonTap: () {
        if (_currentPage != 0) {
          _previousPage();
        } else {
          Get.back();
        }
      },
      body: PageView(
        clipBehavior: Clip.none,
        controller: _pageController,
        physics: const NeverScrollableScrollPhysics(),
        children: [
          RequestMoneyContactSelectPage(
            onSuccessCallback: () {
              _nextPage(goToPage: 1);
            },
          ),
          RequestMoneyAmountPage(
            onSuccessCallback: () {
              _nextPage(goToPage: 2);
            },
            context: context,
          ),
          RequestMoneyPinVerificationPage(context: context),
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
                  Get.find<RequestMoneyController>().checkUserExist(
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
