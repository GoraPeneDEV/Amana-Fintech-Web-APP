import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/buttons/custom_elevated_button.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/custom_contact_list_tile_card.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/app/components/image/my_network_image_widget.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/app/components/text/header_text.dart';
import 'package:ovopay/app/components/text-field/rounded_text_field.dart';
import 'package:ovopay/app/screens/cash_out_screen_screen/controller/cash_out_controller.dart';
import 'package:ovopay/app/screens/cash_out_screen_screen/views/widgets/cash_out_amount_page.dart';
import 'package:ovopay/app/screens/cash_out_screen_screen/views/widgets/cash_out_pin_page.dart';
import 'package:ovopay/core/data/models/global/qr_code/scan_qr_code_response_model.dart';
import 'package:ovopay/core/data/models/user/user_model.dart';
import 'package:ovopay/core/data/repositories/modules/cash_out/cash_out_repo.dart';
import 'package:ovopay/core/route/route.dart';
import 'package:skeletonizer/skeletonizer.dart';

import '../../../../../core/utils/util_exporter.dart';

class CashOutScreen extends StatefulWidget {
  const CashOutScreen({super.key, this.toUserInformation});
  final UserModel? toUserInformation;
  @override
  State<CashOutScreen> createState() => _CashOutScreenState();
}

class _CashOutScreenState extends State<CashOutScreen> {
  final PageController _pageController = PageController();
  int _currentPage = 0;

  @override
  void initState() {
    Get.put(CashOutRepo());
    final controller = Get.put(CashOutController(cashOutRepo: Get.find()));

    super.initState();
    // Add listener to track page changes
    _pageController.addListener(_pageChangeListener);

    WidgetsBinding.instance.addPostFrameCallback((v) async {
      await controller.initController();
      if (widget.toUserInformation != null) {
        controller.setAgentFromQrScan(() {
          _nextPage(goToPage: 1);
        }, existUserModel: widget.toUserInformation);
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
      pageTitle: MyStrings.cashOut,
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
              Get.toNamed(RouteHelper.cashOutHistoryScreen);
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
        physics: const NeverScrollableScrollPhysics(),
        children: [
          _buildContactSelectPage(),
          CashOutAmountPage(
            onSuccessCallback: () {
              _nextPage(goToPage: 2);
            },
            context: context,
          ),
          CashOutPinVerificationPage(context: context),
        ],
      ),
    );
  }

  //Contact Select Widget
  Widget _buildContactSelectPage() {
    return GetBuilder<CashOutController>(
      builder: (cashOutController) {
        return SingleChildScrollView(
          clipBehavior: Clip.none,
          child: Column(
            children: [
              Skeletonizer(
                enabled: cashOutController.isPageLoading,
                child: Padding(
                  padding: EdgeInsets.only(bottom: Dimensions.space16.h),
                  child: CustomAppCard(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        HeaderText(
                          textAlign: TextAlign.center,
                          text: MyStrings.agent.tr,
                          textStyle: MyTextStyle.sectionTitle.copyWith(
                            color: MyColor.getHeaderTextColor(),
                          ),
                        ),
                        spaceDown(Dimensions.space8),
                        RoundedTextField(
                          controller: cashOutController.phoneNumberOrUserNameController,
                          showLabelText: false,
                          labelText: MyStrings.usernameOrPhoneHint.tr,
                          hintText: MyStrings.usernameOrPhoneHint.tr,
                          textInputAction: TextInputAction.next,
                          keyboardType: TextInputType.text,
                          suffixIcon: IconButton(
                            onPressed: () {
                              cashOutController.checkAgentExist(() {
                                _nextPage(goToPage: 1);
                              });
                            },
                            icon: MyAssetImageWidget(
                              color: MyColor.getPrimaryColor(),
                              width: 20.sp,
                              height: 20.sp,
                              boxFit: BoxFit.contain,
                              assetPath: MyIcons.arrowForward,
                              isSvg: true,
                            ),
                          ),
                          onChanged: (value) {},
                        ),
                        if (cashOutController.latestCashOutHistory.isNotEmpty) ...[
                          spaceDown(Dimensions.space16),
                          HeaderText(
                            text: MyStrings.recent.tr,
                            textStyle: MyTextStyle.sectionTitle2.copyWith(
                              color: MyColor.getHeaderTextColor(),
                            ),
                          ),
                          spaceDown(Dimensions.space8),
                          ...List.generate(
                            cashOutController.latestCashOutHistory.length,
                            (index) {
                              var item = cashOutController.latestCashOutHistory[index];
                              bool isLastIndex = index == cashOutController.latestCashOutHistory.length - 1;
                              return CustomContactListTileCard(
                                leading: MyNetworkImageWidget(
                                  width: Dimensions.space40.w,
                                  height: Dimensions.space40.w,
                                  radius: Dimensions.radiusProMax.r,
                                  isProfile: true,
                                  imageUrl: item.receiverAgent?.getAgentImageUrl() ?? "",
                                  imageAlt: item.receiverAgent?.getFullName(),
                                ),
                                showBorder: !isLastIndex,
                                imagePath: item.receiverAgent?.getAgentImageUrl(),
                                title: item.receiverAgent?.getFullName(),
                                subtitle: "+${item.receiverAgent?.getUserMobileNo(withCountryCode: true)}",
                                onPressed: () {
                                  cashOutController.checkAgentExist(
                                    () {
                                      _nextPage(goToPage: 1);
                                    },
                                    inputUserNameOrPhone: item.receiverAgent?.username ?? "",
                                  );
                                },
                              );
                            },
                          ),
                        ],
                      ],
                    ),
                  ),
                ),
              ),
              CustomElevatedBtn(
                elevation: 0,
                radius: Dimensions.largeRadius.r,
                bgColor: MyColor.getWhiteColor(),
                textColor: MyColor.getPrimaryColor(),
                text: MyStrings.tapToScanQRcode.tr,
                borderColor: MyColor.getPrimaryColor(),
                shadowColor: MyColor.getWhiteColor(),
                icon: Padding(
                  padding: EdgeInsets.symmetric(vertical: Dimensions.space7.h),
                  child: MyAssetImageWidget(
                    width: Dimensions.space40.w,
                    height: Dimensions.space40.w,
                    boxFit: BoxFit.contain,
                    assetPath: MyIcons.scanQrCodeIconIcon,
                    color: MyColor.getPrimaryColor(),
                    isSvg: true,
                  ),
                ),
                onTap: () async {
                  Get.toNamed(
                    RouteHelper.scanQrCodeScreen,
                    arguments: MyStrings.scanAgentQrCode,
                  )?.then((v) {
                    ScanQrCodeResponseModel scanQrCodeResponseModel = v as ScanQrCodeResponseModel;
                    if (scanQrCodeResponseModel.data?.userType != AppStatus.USER_TYPE_AGENT) {
                      CustomSnackBar.error(
                        errorList: [MyStrings.pleaseScanAgentQRCode],
                      );
                      return;
                    }
                    Get.find<CashOutController>().checkAgentExist(
                      inputUserNameOrPhone: scanQrCodeResponseModel.data?.userData?.username ?? "",
                      () {
                        _nextPage(goToPage: 1);
                      },
                    );
                  });
                },
              ),
            ],
          ),
        );
      },
    );
  }
}
