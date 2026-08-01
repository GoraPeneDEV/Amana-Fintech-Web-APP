import 'package:flutter/material.dart';
import 'package:ovopay/app/components/shimmer/all_ticket_shimmer.dart';
import 'package:ovopay/core/utils/util_exporter.dart';

class ListTileShimmer extends StatelessWidget {
  const ListTileShimmer({super.key});

  @override
  Widget build(BuildContext context) {
    return ListView.separated(
      itemCount: 10,
      physics: const AlwaysScrollableScrollPhysics(
        parent: ClampingScrollPhysics(),
      ),
      shrinkWrap: true,
      separatorBuilder: (context, index) => Container(
        margin: const EdgeInsets.only(bottom: Dimensions.space10),
      ),
      itemBuilder: (BuildContext context, int index) {
        return const TicketShimmer();
      },
    );
  }
}
