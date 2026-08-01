import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/app/components/shimmer/card_screen_shimmer.dart';
import 'package:ovopay/app/screens/virtual_cards/controller/virtual_cards_controller.dart';
import 'package:ovopay/app/screens/virtual_cards/views/widgets/single_vc_card_widget.dart';
import 'package:ovopay/app/screens/virtual_cards/views/widgets/single_vc_cards_transaction_list.dart';
import 'package:ovopay/core/utils/util_exporter.dart';

class SingleCardsScreen extends StatefulWidget {
  const SingleCardsScreen({super.key, required this.cardId});
  final String cardId;

  @override
  State<SingleCardsScreen> createState() => _SingleCardsScreenState();
}

class _SingleCardsScreenState extends State<SingleCardsScreen> {
  final ScrollController historyScrollController = ScrollController();
  @override
  void initState() {
    VirtualCardsController controller = Get.find();

    super.initState();

    WidgetsBinding.instance.addPostFrameCallback((v) async {
      await controller.loadSingleVirtualCardInfo(widget.cardId);
    });
  }

  @override
  Widget build(BuildContext context) {
    return GetBuilder<VirtualCardsController>(
      builder: (controller) {
        return MyCustomScaffold(
          pageTitle: MyStrings.cardDetails,
          body: controller.isSingleCardLoading
              ? CardScreenShimmer()
              : RefreshIndicator(
                  onRefresh: () async {
                    await controller.loadSingleVirtualCardInfo(widget.cardId);
                  },
                  child: NestedScrollView(
                    physics: AlwaysScrollableScrollPhysics(),
                    headerSliverBuilder: (
                      BuildContext context,
                      bool innerBoxIsScrolled,
                    ) {
                      return <Widget>[
                        SliverToBoxAdapter(
                          child: Padding(
                            padding: EdgeInsets.only(
                              bottom: Dimensions.space16.h,
                            ),
                            child: SingleVcCardWidget(),
                          ),
                        ),
                      ];
                    },
                    body: SingleCardTransactionListCard(
                      historyScrollController: historyScrollController,
                    ),
                  ),
                ),
        );
      },
    );
  }
}
