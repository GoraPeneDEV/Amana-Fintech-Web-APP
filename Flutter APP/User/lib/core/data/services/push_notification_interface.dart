abstract class PushNotificationInterface {
  Future<bool> sendUserToken();

  Future<bool> sendUpdatedToken(String deviceToken);

  Map<String, String> deviceTokenMap(String deviceToken);
}
