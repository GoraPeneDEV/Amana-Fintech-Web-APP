import 'package:flutter/material.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/core/utils/util_exporter.dart';
import 'my_shimmer_widget.dart';

class TicketDetailsShimmer extends StatelessWidget {
  const TicketDetailsShimmer({super.key});

  @override
  Widget build(BuildContext context) {
    return SingleChildScrollView(
      child: Column(
        children: [
          CustomAppCard(
            child: Column(
              children: [
                Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Flexible(
                      child: Padding(
                        padding: const EdgeInsetsDirectional.only(end: 10),
                        child: Column(
                          children: [
                            MyShimmerWidget(
                              child: Container(
                                width: double.infinity,
                                height: 15,
                                color: MyColor.getWhiteColor(),
                              ),
                            ),
                            const SizedBox(height: 5),
                            MyShimmerWidget(
                              child: Container(
                                width: double.infinity,
                                height: 12,
                                color: MyColor.getWhiteColor(),
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                    MyShimmerWidget(
                      child: Container(
                        width: 60,
                        height: 15,
                        decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(4),
                          color: MyColor.getWhiteColor(),
                        ),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 15),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    MyShimmerWidget(
                      child: Container(
                        width: 60,
                        height: 15,
                        decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(4),
                          color: MyColor.getWhiteColor(),
                        ),
                      ),
                    ),
                    MyShimmerWidget(
                      child: Container(
                        width: 100,
                        height: 12,
                        color: MyColor.getWhiteColor(),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 15),
                MyShimmerWidget(
                  child: CustomAppCard(
                    width: double.infinity,
                    height: 150,
                    child: SizedBox(),
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 20),
          ...List.generate(5, (index) {
            return CustomAppCard(
              margin: EdgeInsetsDirectional.only(bottom: Dimensions.space20),
              child: Column(
                children: [
                  Row(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Flexible(
                        child: Padding(
                          padding: const EdgeInsetsDirectional.only(end: 10),
                          child: Column(
                            children: [
                              MyShimmerWidget(
                                child: Container(
                                  width: double.infinity,
                                  height: 50,
                                  color: MyColor.getWhiteColor(),
                                ),
                              ),
                              const SizedBox(height: 5),
                              MyShimmerWidget(
                                child: Container(
                                  width: double.infinity,
                                  height: 12,
                                  color: MyColor.getWhiteColor(),
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                      MyShimmerWidget(
                        child: Container(
                          width: 60,
                          height: 15,
                          decoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(4),
                            color: MyColor.getWhiteColor(),
                          ),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 15),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      MyShimmerWidget(
                        child: Container(
                          width: 60,
                          height: 15,
                          decoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(4),
                            color: MyColor.getWhiteColor(),
                          ),
                        ),
                      ),
                      MyShimmerWidget(
                        child: Container(
                          width: 100,
                          height: 12,
                          color: MyColor.getWhiteColor(),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            );
          }),
        ],
      ),
    );
  }
}
