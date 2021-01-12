import styled from 'styled-components'
import AudioPlayer from 'react-h5-audio-player'

export const StyledPlayerWrapper = styled.div`
  padding: 32px;
  width: 100%;
  background-color: #000;
`
export const StyledTextWrapper = styled.div`
  display: flex;
  justify-content: space-between;
`
export const StyledText = styled.p`
  flex-basis: 270px;
  color: #fff;
  font-size: 14px;
`
export const StyledFormWrapper = styled.div`
  display: flex;
  justify-content: space-between;
  align-items: center;
`
export const StyledForm = styled.form`
  display: flex;
  margin-bottom: 38px;
`
export const StyledRadio = styled.input`
  width: 1px;
  height: 1px;
  outline: none;
  &:checked {
    outline: none;
  }
  &:checked::after {
    position: absolute;
    content: '';
    width: 34px;
    height: 2px;
    background-color: #fff;
    top: 22px;
    left: 50%;
    transform: translateX(-50%);
  }
`
export const StyledLabel = styled.label`
  position: relative;
  color: #fff;
  font-size: 14px;
  cursor: pointer;
  &:not(:last-child) {
    margin-right: 24px;
  }
  &:hover::after {
    position: absolute;
    content: '';
    width: 100%;
    height: 2px;
    background-color: #fff;
    top: 22px;
    left: 0;
  }
`

export const StyledAudioPlayer = styled(AudioPlayer)`
  background-color: #000;
`
export const StyledVideoWrapper = styled.div`
  display: flex;
  justify-content: center;
  align-items: center;
`
export const StyledNotes = styled.img``
