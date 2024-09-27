import apiRequest from "../lib/api";

export const checkUserPreferences = async (access_token, navigate, redirectOnSuccess = "/") => {
  try {
    const res = await apiRequest.get("/preferences", {
      headers: {
        Authorization: `Bearer ${access_token}`,
      },
    });

    const preferences = res.data.preferences?.settings || {};

    const isPreferencesEmpty =
      (!preferences.authors || preferences.authors.length === 0) &&
      (!preferences.source_ids || preferences.source_ids.length === 0) &&
      (!preferences.category_ids || preferences.category_ids.length === 0);

    if (isPreferencesEmpty) {
      navigate("/preferences");
    } else {
      navigate(redirectOnSuccess)
    }
  } catch (err) {
    console.error("Error fetching user preferences:", err);
  }
};
