import { useEffect } from 'react';
import { View, Text, StyleSheet, TextInput, TouchableWithoutFeedback, ScrollView } from 'react-native'

const FormWiadomosci = () => {
  const loadMore = ()=>{}
  
  useEffect(()=>{
    // załadować dane
    // ustawić w dobrym miejscu scroll
    // umożliwić doładowywanie danych
  });
  return (
    <View>
        <ScrollView
            onMomentumScrollBegin={loadMore}
        >

        </ScrollView>
        <View>
            <TextInput />
            <TouchableWithoutFeedback>
                <View>
                </View>
            </TouchableWithoutFeedback>
        </View>
    </View>
  )
}

const style = StyleSheet.create({
    main: {}
});

export default FormWiadomosci
