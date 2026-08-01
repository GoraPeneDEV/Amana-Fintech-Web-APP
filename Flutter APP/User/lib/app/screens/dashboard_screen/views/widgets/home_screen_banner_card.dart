import 'package:cached_network_image/cached_network_image.dart';
import 'package:carousel_slider/carousel_slider.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/screens/dashboard_screen/controller/home_controller.dart';
import 'package:ovopay/core/route/route.dart';
import 'package:ovopay/core/utils/util_exporter.dart';
import 'package:skeletonizer/skeletonizer.dart';

class HomeScreenBannerCard extends StatefulWidget {
  const HomeScreenBannerCard({super.key});

  @override
  State<HomeScreenBannerCard> createState() => _HomeScreenBannerCardState();
}

class _HomeScreenBannerCardState extends State<HomeScreenBannerCard> {
  int _current = 0;
  final CarouselSliderController _controller = CarouselSliderController();

  @override
  Widget build(BuildContext context) {
    return GetBuilder<HomeController>(
      builder: (homeController) {
        return homeController.bannersList.isEmpty
            ? SizedBox.shrink()
            : Column(
                children: [
                  Container(
                    margin: EdgeInsets.only(top: Dimensions.space20.h),
                    child: CarouselSlider.builder(
                      carouselController: _controller,
                      itemCount: (homeController.bannersList).length,
                      options: CarouselOptions(
                        autoPlay: true,
                        clipBehavior: Clip.none,
                        height: 140.0,
                        viewportFraction: 1,
                        enlargeFactor: 1,
                        enlargeCenterPage: true,
                        enableInfiniteScroll: true,
                        onPageChanged: (index, reason) {
                          setState(() {
                            _current = index;
                          });
                        },
                      ),
                      itemBuilder: (
                        BuildContext context,
                        int itemIndex,
                        int pageViewIndex,
                      ) {
                        var item = homeController.bannersList[itemIndex];
                        return GestureDetector(
                          onTap: () {
                            if (item.type == "1") {
                              MyUtils.launchUrlToBrowser(item.link ?? "");
                            }
                            if (item.type == "2") {
                              RouteHelper.goToModuleRoute(
                                moduleName: item.link ?? "",
                              );
                            }
                          },
                          child: CachedNetworkImage(
                            imageUrl: item.getBannerImageUrl() ?? "",
                            fit: BoxFit.contain,
                            placeholder: (context, url) => Skeletonizer(
                              child: Bone(
                                width: double.infinity,
                                height: 140.0,
                                borderRadius: BorderRadius.circular(
                                  Dimensions.cardRadius.r,
                                ),
                              ),
                            ),
                            errorWidget: (context, url, error) => CustomAppCard(
                              width: double.infinity,
                              child: Icon(
                                Icons.image,
                                color: MyColor.getBodyTextColor(),
                                size: 140 / 3,
                              ),
                            ),
                          ),
                        );
                      },
                    ),
                  ),

                  //Make dot indicators
                  Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: (homeController.bannersList).asMap().entries.map((entry) {
                      return GestureDetector(
                        onTap: () => _controller.animateToPage(entry.key),
                        child: Container(
                          width: _current == entry.key ? Dimensions.space16.h : Dimensions.space8.w,
                          height: Dimensions.space8.h,
                          margin: EdgeInsets.symmetric(
                            vertical: 8.0,
                            horizontal: 4.0,
                          ),
                          decoration: BoxDecoration(
                            shape: BoxShape.rectangle,
                            borderRadius: BorderRadius.circular(
                              Dimensions.largeRadius.r,
                            ),
                            color: _current == entry.key ? MyColor.getPrimaryColor() : MyColor.getBorderColor(),
                          ),
                        ),
                      );
                    }).toList(),
                  ),
                ],
              );
      },
    );
  }
}
