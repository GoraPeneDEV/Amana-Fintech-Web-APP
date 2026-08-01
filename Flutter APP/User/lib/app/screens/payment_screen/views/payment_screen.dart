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
import 'package:ovopay/app/screens/payment_screen/controller/payment_controller.dart';
import 'package:ovopay/app/screens/payment_screen/views/widgets/payment_amount_page.dart';
import 'package:ovopay/app/screens/payment_screen/views/widgets/payment_pin_page.dart';
import 'package:ovopay/core/data/models/global/qr_code/scan_qr_code_response_model.dart';
import 'package:ovopay/core/data/models/user/user_model.dart';
import 'package:ovopay/core/data/repositories/modules/make_payment/make_payment_repo.dart';
import 'package:ovopay/core/route/route.dart';
import 'package:skeletonizer/skeletonizer.dart';

import '../../../../../core/utils/util_exporter.dart';

class PaymentScreen extends StatefulWidget {
  const PaymentScreen({super.key, this.toUserInformation});
  final UserModel? toUserInformation;
  @override
  State<PaymentScreen> createState() => _PaymentScreenState();
}

class _PaymentScreenState extends State<PaymentScreen> {
  final PageController _pageController = PageController();
  int _currentPage = 0;

  @override
  void initState() {
    Get.put(MakePaymentRepo());
    final controller = Get.put(PaymentController(makePaymentRepo: Get.find()));

    super.initState();
    // Add listener to track page changes
    _pageController.addListener(_pageChangeListener);
    WidgetsBinding.instance.addPostFrameCallback((v) async {
      await controller.initController();
      if (widget.toUserInformation != null) {
        controller.setMerchantFromQrScan(() {
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
      pageTitle: MyStrings.payment,
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
              Get.toNamed(RouteHelper.paymentHistoryScreen);
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
          PaymentAmountPage(
            onSuccessCallback: () {
              _nextPage(goToPage: 2);
            },
            context: context,
          ),
          PaymentPinVerificationPage(context: context),
        ],
      ),
    );
  }

  //Contact Select Widget
  Widget _buildContactSelectPage() {
    return GetBuilder<PaymentController>(
      builder: (paymentController) {
        return SingleChildScrollView(
          clipBehavior: Clip.none,
          child: Column(
            children: [
              Skeletonizer(
                enabled: paymentController.isPageLoading,
                child: Padding(
                  padding: EdgeInsets.only(bottom: Dimensions.space16.h),
                  child: CustomAppCard(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        HeaderText(
                          textAlign: TextAlign.center,
                          text: MyStrings.to.tr,
                          textStyle: MyTextStyle.sectionTitle.copyWith(
                            color: MyColor.getHeaderTextColor(),
                          ),
                        ),
                        spaceDown(Dimensions.space8),
                        RoundedTextField(
                          controller: paymentController.phoneNumberOrUserNameController,
                          showLabelText: false,
                          labelText: MyStrings.usernameOrPhoneHint.tr,
                          hintText: MyStrings.usernameOrPhoneHint.tr,
                          textInputAction: TextInputAction.next,
                          keyboardType: TextInputType.text,
                          suffixIcon: IconButton(
                            onPressed: () {
                              paymentController.checkMerchantExist(() {
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
                        if (paymentController.latestMakePaymentHistory.isNotEmpty) ...[
                          spaceDown(Dimensions.space16),
                          HeaderText(
                            text: MyStrings.recent.tr,
                            textStyle: MyTextStyle.sectionTitle2.copyWith(
                              color: MyColor.getHeaderTextColor(),
                            ),
                          ),
                          spaceDown(Dimensions.space8),
                          ...List.generate(
                            paymentController.latestMakePaymentHistory.length,
                            (index) {
                              var item = paymentController.latestMakePaymentHistory[index];
                              bool isLastIndex = index == paymentController.latestMakePaymentHistory.length - 1;
                              return CustomContactListTileCard(
                                leading: MyNetworkImageWidget(
                                  width: Dimensions.space40.w,
                                  height: Dimensions.space40.w,
                                  radius: Dimensions.radiusProMax.r,
                                  isProfile: true,
                                  imageUrl: item.merchant?.getMerchantImageUrl() ?? "",
                                  imageAlt: item.merchant?.getFullName(),
                                ),
                                showBorder: !isLastIndex,
                                imagePath: item.merchant?.getMerchantImageUrl(),
                                title: item.merchant?.getFullName(),
                                subtitle: "+${item.merchant?.getUserMobileNo(withCountryCode: true)}",
                                onPressed: () {
                                  paymentController.checkMerchantExist(
                                    () {
                                      _nextPage(goToPage: 1);
                                    },
                                    inputUserNameOrPhone: item.merchant?.username ?? "",
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
                  padding: EdgeInsets.symmetric(vertical: Dimensions.space5.h),
                  child: MyAssetImageWidget(
                    width: 40.w,
                    height: 40.w,
                    boxFit: BoxFit.contain,
                    assetPath: MyIcons.walletQrCodeIcon,
                    color: MyColor.getPrimaryColor(),
                    isSvg: true,
                  ),
                ),
                onTap: () async {
                  Get.toNamed(
                    RouteHelper.scanQrCodeScreen,
                    arguments: MyStrings.scanMerchantQrCode,
                  )?.then((v) {
                    ScanQrCodeResponseModel scanQrCodeResponseModel = v as ScanQrCodeResponseModel;
                    if (scanQrCodeResponseModel.data?.userType != AppStatus.USER_TYPE_MERCHANT) {
                      CustomSnackBar.error(
                        errorList: [MyStrings.pleaseScanMerchantQRCode],
                      );
                      return;
                    }
                    Get.find<PaymentController>().checkMerchantExist(
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
